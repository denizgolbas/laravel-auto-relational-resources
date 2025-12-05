<?php

namespace DenizGolbas\LaravelAutoRelationalResources\Tests;

use DenizGolbas\LaravelAutoRelationalResources\AutoRelationalResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;

class AutoRelationalResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set default config
        Config::set('auto-relational-resources.model_namespace', 'DenizGolbas\\LaravelAutoRelationalResources\\Tests\\Models');
        Config::set('auto-relational-resources.resource_namespace', 'DenizGolbas\\LaravelAutoRelationalResources\\Tests\\Resources');
        Config::set('auto-relational-resources.auto_load_relations', true);
        Config::set('auto-relational-resources.max_depth', null);
    }

    public function test_resource_can_be_instantiated()
    {
        $resource = new class(['id' => 1]) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
        };

        $this->assertInstanceOf(JsonResource::class, $resource);
        $this->assertInstanceOf(AutoRelationalResource::class, $resource);
    }

    public function test_resource_returns_basic_data()
    {
        $model = new class extends Model {
            protected $fillable = ['id', 'name'];
        };
        $model->id = 1;
        $model->name = 'Test';

        $resource = new class($model) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
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

        $resource = new class($model) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('modelResourceNameResolver');
        $method->setAccessible(true);

        $result = $method->invoke($resource, $model);

        $this->assertIsString($result);
    }

    public function test_collection_resource_name_resolver()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };

        $collection = new Collection([$model]);

        $resource = new class($model) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
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

        $resource = new class($model) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
        };

        $reflection = new \ReflectionClass($resource);
        $method = $reflection->getMethod('collectionNameResolver');
        $method->setAccessible(true);

        $result = $method->invoke($resource, $collection);

        $this->assertEquals('', $result);
    }

    public function test_merge_relations_without_relations()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };
        $model->id = 1;

        $resource = new class($model) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
        };

        $request = Request::create('/');
        $result = $resource->toArray($request);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(1, $result['id']);
    }

    public function test_auto_load_relations_can_be_disabled()
    {
        Config::set('auto-relational-resources.auto_load_relations', false);

        $model = new class extends Model {
            protected $table = 'test_models';
        };
        $model->id = 1;

        $resource = new class($model) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
        };

        $request = Request::create('/');
        $result = $resource->toArray($request);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(1, $result['id']);
    }

    public function test_with_method_returns_meta()
    {
        $model = new class extends Model {
            protected $table = 'test_models';
        };

        $resource = new class($model) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
        };

        $request = Request::create('/');
        $result = $resource->with($request);

        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('version', $result['meta']);
    }
}
