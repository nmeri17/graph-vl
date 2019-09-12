# graph-lvel
Laravel GraphQL implementation

This simple project was created because the most famous Laravel implementation of graphql is too verbose. While the pro to using is how the library grants its user full control over his data structures and query execution point (via `GraphQL::execute($schema, ...$otherParams)`). PHP-Lighthouse isn't verbose, but its constrictions include using directives for mutation, and providing no entrance point for supplying queries from the server side.

So this project aims to bridge the gap between the pain-points of both projects.
For those unfamiliar with GraphQl, it's a concept that allows you request and receive specific data thereby reducing your payload, with minimal database calls i.e. higher response time. By phasing out RESTful endpoints, you're in a better position to request resources across multiple models/nodes.

In addition, it supports pagination and relationship fetching

# How to use
You can either call it from your web controllers, or post the same payload as you would in a web controller, to a single endpoint. This endpoint then returns all the resources described in your schema

* In your web controller, instead of making requests for each of these nodes, you have *

        $app = [
            'Model1' => ['limit' => 4, 'fields' => ['list_of_criteria', 'etc' ], 'attributes' => ['store_id'], 'relations' => ['store:id,name,custom_url','product']], // trim results on the relatives as well

            'Model2' => ['limit' => 7, 'fields' => [ 'list1','list2', 'list3'], 'attributes' => ['name', 'qty'], 'relations' => ['store:id,name,custom_url','user:id,name'], 'page' => 3],

            'Model3' => [ 'fields' => ['all'], 'attributes' => ['desktop_image']] // note special variable name `all` used to grab all rows on the model's table
        ];
		
		$temp = json_encode(FrontController::getHandler($app));

        dd(json_decode($temp, true)); // pipe $temp to your view

The above `$app` variable can also be posted as a JSON object with the opening key "schema" to any endpoint on your API route list. Just ensure you're calling `FrontController::getHandler` with that payload as shown above.

The last step is defining the schema criteria in /AccessorQueries/ModelName

```

namespace App\Http\Controllers\Api\AccessorQueries;

use App\Http\Controllers\Api\AccessorQueries\Contracts\QueryAccessorInterface;

use App\Models\Model2 as AnotherModel;


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

 ```

# TO-DO
-    Convert to a proper, composer-installable package
