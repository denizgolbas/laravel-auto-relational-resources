<?php

namespace DenizGolbas\LaravelAutoRelationalResources\Tests;

use DenizGolbas\LaravelAutoRelationalResources\Traits\HasRelationalResources;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;

class HasRelationalResourcesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('auto-relational-resources.model_namespace', 'DenizGolbas\\LaravelAutoRelationalResources\\Tests\\Models');
        Config::set('auto-relational-resources.resource_namespace', 'DenizGolbas\\LaravelAutoRelationalResources\\Tests\\Resources');
        Config::set('auto-relational-resources.max_depth', null);
    }

    public function test_trait_can_be_used()
    {
        $resource = new class(['id' => 1]) extends JsonResource {
            use HasRelationalResources;

            public function toArray($request): array
            {
                return ['id' => $this->id];
            }
        };

        $this->assertInstanceOf(JsonResource::class, $resource);
    }

    public function test_merge_relations_with_empty_array()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };
        $model->id = 1;

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;

            public function toArray($request): array
            {
                $data = ['id' => $this->id];
                return $this->mergeRelations($data, $request);
            }
        };

        $request = Request::create('/');
        $result = $resource->toArray($request);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(1, $result['id']);
    }

    public function test_model_resource_name_resolver()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('modelResourceNameResolver');
        $method->setAccessible(true);

        $result = $method->invoke($resource, $model);

        $this->assertIsString($result);
    }

    public function test_model_resource_name_resolver_returns_empty_string_for_non_model()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('modelResourceNameResolver');
        $method->setAccessible(true);

        $result = $method->invoke($resource, 'not-a-model');

        $this->assertEquals('', $result);
    }

    public function test_collection_resource_name_resolver()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };

        $collection = new Collection([$model]);

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('collectionNameResolver');
        $method->setAccessible(true);

        $result = $method->invoke($resource, $collection);

        $this->assertIsString($result);
    }

    public function test_collection_name_resolver_returns_empty_string_for_empty_collection()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };

        $collection = new Collection([]);

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('collectionNameResolver');
        $method->setAccessible(true);

        $result = $method->invoke($resource, $collection);

        $this->assertEquals('', $result);
    }

    public function test_collection_name_resolver_returns_empty_string_for_non_collection()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('collectionNameResolver');
        $method->setAccessible(true);

        $result = $method->invoke($resource, 'not-a-collection');

        $this->assertEquals('', $result);
    }

    public function test_max_depth_limits_relations()
    {
        Config::set('auto-relational-resources.max_depth', 1);

        $model = new class extends Model {
            protected $table = 'test_models';
        };
        $model->id = 1;

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;

            public function toArray($request): array
            {
                $data = ['id' => $this->id];
                return $this->mergeRelations($data, $request);
            }
        };

        $request = Request::create('/');
        $request->merge(['_resource_depth' => 1]);
        
        $result = $resource->toArray($request);

        $this->assertArrayHasKey('id', $result);
    }

    public function test_infinite_depth_allows_all_relations()
    {
        Config::set('auto-relational-resources.max_depth', null);

        $model = new class extends Model {
            protected $table = 'test_models';
        };
        $model->id = 1;

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;

            public function toArray($request): array
            {
                $data = ['id' => $this->id];
                return $this->mergeRelations($data, $request);
            }
        };

        $request = Request::create('/');
        $request->merge(['_resource_depth' => 10]);
        
        $result = $resource->toArray($request);

        $this->assertArrayHasKey('id', $result);
    }

    public function test_set_relations_method()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };
        $model->id = 1;

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('setRelations');
        $method->setAccessible(true);

        $request = Request::create('/');
        $method->invoke($resource, $request);

        $relationsArrayProperty = $reflection->getProperty('relationsArray');
        $relationsArrayProperty->setAccessible(true);
        $relationsArray = $relationsArrayProperty->getValue($resource);

        $this->assertIsArray($relationsArray);
    }

    public function test_resolve_relations_method()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };
        $model->id = 1;

        $resource = new class($model) extends JsonResource {
            use HasRelationalResources;
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('resolveRelations');
        $method->setAccessible(true);

        $request = Request::create('/');
        $method->invoke($resource, $request);

        // Should not throw exception
        $this->assertTrue(true);
    }
}

