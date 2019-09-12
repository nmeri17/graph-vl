<?php 

namespace App\Http\Controllers\Api\AccessorQueries;

use App\Http\Controllers\Api\AccessorQueries\Contracts\QueryAccessorInterface;

use App\Models\Model1 as ModelName;

/**
 * queries on the advert model
 */
class Model1 implements QueryAccessorInterface {
	
	private $baseQuery;

	private $nodes;

	private $schema;

	function __construct(array $schema) {

		$this->schema = $schema;

		$this->baseQuery = ModelName::limit(@$schema['limit'] ?? 10);

		$this->setNodes();

      	$this->sort();
	}

	public function sort () { }

	public function getBaseQuery () {

      return $this->baseQuery;
  	}

	public function getNodes () {

      return $this->nodes;
  	}

	public function setNodes () {

      $this->nodes = [

          'list_of_criteria' => ['where' => ['column1', '<>', null ]],

          'etc' => ['where' => ['column2' => 'targe value' ]]
      	];
  	}

	public function getSchema () {

      return $this->schema;
  	}

	public function getDefaultRelations () {

      return [ ];
  	}
}
 ?>