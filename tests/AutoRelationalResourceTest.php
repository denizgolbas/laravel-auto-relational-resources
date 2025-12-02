<?php

namespace DenizGolbas\LaravelAutoRelationalResources\Tests;

use DenizGolbas\LaravelAutoRelationalResources\AutoRelationalResource;
use Illuminate\Http\Resources\Json\JsonResource;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class AutoRelationalResourceTest extends TestCase
{
    public function test_resource_can_be_instantiated()
    {
        $resource = new class(['id' => 1]) extends AutoRelationalResource {
            public function toArray($request): array
            {
                return parent::toArray($request);
            }
        };

        $this->assertInstanceOf(JsonResource::class, $resource);
    }
}

