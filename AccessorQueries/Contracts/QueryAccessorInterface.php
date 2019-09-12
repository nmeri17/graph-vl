<?php

namespace App\Http\Controllers\Api\AccessorQueries\Contracts;

interface QueryAccessorInterface {
	
	// mutates $this->nodes for custom  behaviour over your criteria map
	public function sort ();

	public function getBaseQuery ();

	public function getNodes ();

	public function getDefaultRelations ();

	public function setNodes ();

	public function getSchema ();
}
?>