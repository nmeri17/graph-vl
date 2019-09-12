<?php 

namespace App\Http\Controllers\Api\AccessorQueries;

use App\Http\Controllers\Api\AccessorQueries\Contracts\QueryAccessorInterface;

use App\Models\Model2 as AnotherModel;

/**
 * queries on the advert model
 */
class Model2 implements QueryAccessorInterface {
	
	private $baseQuery;

	private $nodes;

	private $schema;

	function __construct(array $schema) {

		$this->schema = $schema;

		$this->baseQuery = AnotherModel::orderBy('created_at', 'desc')->inRandomOrder();

		$this->setNodes();

      	$this->sort();
	}

	public function sort () {

      foreach ($this->nodes as $key => &$value ) if ($key != 'exclusiveDeals')

        $value['whereHas']['model_rel1'] = ['name' => "Trending fashion advert"];
    }

	public function getBaseQuery () {

      return $this->baseQuery;
  	}

	public function getNodes () {

      return $this->nodes;
  	}

	public function setNodes () {

      $this->nodes = [

          'list3' => ['whereHas' => ['model_rel1' => ['name' => "Exclusive deals of the day"]]],

          'list1', => ['whereHas' => ['model_rel2' => ['cat_id' => 2]]],

          'list2' => ['whereHas' => ['model_rel2' => ['cat_id' => 1]]],
      	];
  	}

	public function getSchema () {

      return $this->schema;
  	}

	public function getDefaultRelations () {

      return ['model_rel1', 'model_rel2', 'model_rel3'];
  	}
}
 ?>