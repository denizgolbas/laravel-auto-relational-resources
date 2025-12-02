<?php

namespace DenizGolbas\LaravelAutoRelationalResources;

use DenizGolbas\LaravelAutoRelationalResources\Traits\HasRelationalResources;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class AutoRelationalResource extends JsonResource
{
    use HasRelationalResources;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
        ];

        // Automatically merge relations if auto_load_relations is enabled
        if (config('auto-relational-resources.auto_load_relations', true)) {
            $data = $this->mergeRelations($data, $request);
        }

        return $data;
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request): array
    {
        return [
            'meta' => [
                'version' => config('auto-relational-resources.version', '1.0.0'),
            ],
        ];
    }
}

