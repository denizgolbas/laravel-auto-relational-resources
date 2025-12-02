<?php

namespace DenizGolbas\LaravelAutoRelationalResources\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasRelationalResources
{
    protected array $relationsArray = [];

    protected array $models = [];

    protected array $collections = [];

    public function mergeRelations(array $resource_array, $request = null): array
    {
        $request = $request ?? request();
        $currentDepth = $request->get('_resource_depth', 0);
        $maxDepth = config('auto-relational-resources.max_depth', null);
        
        // Eğer max_depth null veya 0 ise sonsuz depth'e izin ver
        if ($maxDepth !== null && $maxDepth > 0 && $currentDepth >= $maxDepth) {
            return $resource_array;
        }

        // Depth'i artır
        $request->merge(['_resource_depth' => $currentDepth + 1]);

        $this->setRelations($request);

        // Depth'i geri al
        $request->merge(['_resource_depth' => $currentDepth]);

        return array_merge($resource_array, $this->relationsArray);
    }

    public function setRelations($request = null)
    {
        $this->resolveRelations($request);
        $this->relationsArray = array_merge($this->models, $this->collections);
    }

    public function resolveRelations($request = null)
    {
        $request = $request ?? request();
        $currentDepth = $request->get('_resource_depth', 0);
        $maxDepth = config('auto-relational-resources.max_depth', null);
        
        // Eğer max_depth null veya 0 ise sonsuz depth'e izin ver
        if ($maxDepth !== null && $maxDepth > 0 && $currentDepth >= $maxDepth) {
            return;
        }

        foreach ($this->resource->getRelations() as $key => $item) {
            if ($item instanceof Model) {
                $resourceClass = $this->modelResourceNameResolver($item);

                if (class_exists($resourceClass)) {
                    $this->models = array_merge($this->models, [$key => $resourceClass::make($this->resource->{$key})]);
                }
            }

            if ($item instanceof Collection) {
                $resourceClass = $this->collectionNameResolver($item);

                if (class_exists($resourceClass)) {
                    $this->collections = array_merge($this->collections, [$key => $resourceClass::collection($this->resource->{$key})]);
                }
            }
        }
    }

    public function modelResourceNameResolver(mixed $model): string
    {
        if ($model instanceof Model) {
            $modelClass = get_class($model);
            $modelNamespace = config('auto-relational-resources.model_namespace', 'App\\Models');
            $resourceNamespace = config('auto-relational-resources.resource_namespace', 'App\\Http\\Resources');
            
            return str_replace($modelNamespace, $resourceNamespace, $modelClass) . 'Resource';
        }

        return '';
    }

    public function collectionNameResolver(mixed $collection): string
    {
        if ($collection instanceof Collection && $collection->first()) {
            $firstItem = $collection->first();
            $modelClass = get_class($firstItem);
            $modelNamespace = config('auto-relational-resources.model_namespace', 'App\\Models');
            $resourceNamespace = config('auto-relational-resources.resource_namespace', 'App\\Http\\Resources');
            
            return str_replace($modelNamespace, $resourceNamespace, $modelClass) . 'Resource';
        }

        return '';
    }
}
