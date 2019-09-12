<?php 

namespace App\Http\Controllers\Api;

class FrontController {

	public static function getHandler ($schema) {

        $populate = [];

        foreach ($schema as $model => $definition) {

            $className = __NAMESPACE__ . '\\AccessorQueries\\'. $model;

            $populate[] = (new AccessorQueries\QueryComposer(

                new $className( $definition)
            ))->results;
        }

        return $populate;
    }
}

?>