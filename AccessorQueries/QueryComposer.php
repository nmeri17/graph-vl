<?php

namespace App\Http\Controllers\Api\AccessorQueries;

use App\Http\Controllers\Api\AccessorQueries\Contracts\QueryAccessorInterface;

use Illuminate\support\Str;

class QueryComposer {

	public $results;

	private $allowedProps;

	private $relations;

	private $limit;

	function __construct(QueryAccessorInterface $queryClass) {

		$userInp = $queryClass->getSchema();

		$nodes = $queryClass->getNodes();

		$limit = @$userInp['limit'];

		$max = 10;


		$this->allowedProps = array_keys($nodes);

      	$this->relations = @$userInp['relations'] ?? $queryClass->getDefaultRelations();

      	$this->limit = ( $limit && ($limit < $max) ? $limit : $max) * count($userInp['fields']);

		
		$this->results = $this->queryAccessor(

			$queryClass->getBaseQuery(), $userInp, $nodes
		);
	}

	// this is where most of the action happens. it processes the given schema against queries for this model
    public function queryAccessor ($query, $userInp, $nodeMap ) {

		$counter = 0;

		$fields = $userInp['fields'];

		foreach ($fields as $key => $node) {

			if (in_array($node, $this->allowedProps))

				$this->chainQueries($query, $nodeMap[$node], $counter);

			else break; $counter++;
		}

		if ($counter == count($fields)) {

			$page = @$userInp['page'] ? $this->limit * $userInp['page'] : null;

			$results = $query->with($this->relations)->limit($this->limit)->offset($page)->get(); // can't filter `select(@$userInp['attributes'])` here since we'll still have to unfurl this collection later

			return $this->rehydrate($results, $userInp, $nodeMap );
		}

		elseif (current($fields) == 'all') return $query->with($this->relations)

			->get(@$userInp['attributes']);

		else return ['error' => 1, 'message' => 'Invalid field name ' . $fields[$counter] . ' found'];
    }

    // merges all queries into one
    public function chainQueries($query, $condRow, $index) {
    
		foreach ($condRow as $method => $arrArgs) {

			$method = (($index == 0) &&

				array_search($method, array_keys($condRow)) == 0) || // 1st item in 1st row of conditions?

				Str::startsWith($method, 'or') ?

					$method : Str::camel('or' . $method);

			if (preg_match('/has/i', $method) ) foreach ($arrArgs as $key => $value)

				$query->{$method} ($key, function ($subQ) use ( $value) {

				    $subQ->where($value );
				});

			else $query->{$method} ( $arrArgs);
		}       
  	}

  	// unwrap the compressed queries into single identities
    private function rehydrate($baseCollection, $userInp, $criteria) {

        $results = [];

        $attributes = array_merge(array_map(function($rel) { return @explode(':', $rel)[0];}, $this->relations), @$userInp['attributes']);

        foreach ($userInp['fields'] as $ind => $fieldName) {

			foreach ($criteria[$fieldName] as $method => $arrArgs) {

				if (preg_match('/has/i', $method) ) foreach ($arrArgs as $rel => $wheres) {

					// simulate whereHas
					$results[$fieldName] = $baseCollection->filter(function($model, $modelKey) use ($rel, $wheres ) {

					    return !is_null($model->$rel) &&

					    	empty(
					    		array_diff_assoc($wheres, $model->$rel->toArray())
					    	);
					})

					->map(function ($item, $key) use($attributes) {
    					
    					return $item->only($attributes);
    				})->values()->all();
				}

				// eval each cond 1 after the other
				else foreach ($arrArgs as $cond => $params) {

					if (!is_numeric($cond)) $params = [$cond => $params];

					/* findOrUpdate result set using the current criteria*/
					if (isset($results[$fieldName]) /*&& !$results[$fieldName]->isEmpty()*/) {$ctxualColl = collect($results[$fieldName]); /*dd($ctxualColl, $fieldName);*/}

					else $ctxualColl = $baseCollection;

					$results[$fieldName] = $ctxualColl->filter(function($model) use ( $params, $fieldName) {

						if (count($params) === 1) return empty(array_diff_assoc($params, $model->toArray())); // normal `where`

						[$att, $oper, $operd] = $params;

						return eval('return ' . $model->{$att} . $oper.$operd.';');
					})->map(function ($item, $key) use($attributes) {
    					
    					return $attributes ? $item::make($item->only($attributes)) : $item;
    				})->values()->all();
				}
			}
        }

        return $results;
    }
}

?>