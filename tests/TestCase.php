<?php

namespace DenizGolbas\LaravelAutoRelationalResources\Tests;

use DenizGolbas\LaravelAutoRelationalResources\AutoRelationalResourcesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            AutoRelationalResourcesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}

