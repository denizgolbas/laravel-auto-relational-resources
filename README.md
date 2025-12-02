<div align="center">

# Laravel Auto Relational Resources

![Laravel Auto Relational Resources](art/logo.png)

**Automatic relational resource management for Laravel**

[![Latest Version](https://img.shields.io/packagist/v/denizgolbas/laravel-auto-relational-resources.svg?style=flat-square)](https://packagist.org/packages/denizgolbas/laravel-auto-relational-resources)
[![Total Downloads](https://img.shields.io/packagist/dt/denizgolbas/laravel-auto-relational-resources.svg?style=flat-square)](https://packagist.org/packages/denizgolbas/laravel-auto-relational-resources)
[![License](https://img.shields.io/packagist/l/denizgolbas/laravel-auto-relational-resources.svg?style=flat-square)](https://packagist.org/packages/denizgolbas/laravel-auto-relational-resources)

</div>

This package automatically resolves relational data in Laravel Resource classes.

## Installation

```bash
composer require denizgolbas/laravel-auto-relational-resources
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=config --provider="DenizGolbas\LaravelAutoRelationalResources\AutoRelationalResourcesServiceProvider"
```

## Usage

### Using the Trait (Recommended)

Use the `HasRelationalResources` trait in your existing Resource classes:

```php
<?php

namespace App\Http\Resources;

use DenizGolbas\LaravelAutoRelationalResources\Traits\HasRelationalResources;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use HasRelationalResources;

    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];

        // Automatically merge relations
        return $this->mergeRelations($data, $request);
    }
}
```

### Using AutoRelationalResource Class

Alternatively, you can extend your Resource class from `AutoRelationalResource`:

```php
<?php

namespace App\Http\Resources;

use DenizGolbas\LaravelAutoRelationalResources\AutoRelationalResource;

class UserResource extends AutoRelationalResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
```

The package automatically detects loaded relations and returns them using the corresponding Resource classes.

### Important Notes

**File Structure Requirement:** Collection and Resource files must follow the same path structure as your model files. The package converts the model class namespace to the Resource namespace to find the corresponding Resource class.

Examples:
- Model: `App\Models\User` → Resource: `App\Http\Resources\UserResource`
- Model: `App\Models\Product\Category` → Resource: `App\Http\Resources\Product\CategoryResource`
- Model: `App\Models\Order` → Collection: `App\Http\Resources\OrderCollection` (same rule applies for Collections)

If a Resource class is not found, the relation data will not be included.

**Depth Control:** By default, the package resolves all loaded relations with infinite depth. If you want to control the depth, you can use the `max_depth` configuration setting. If `max_depth` is null or 0, infinite depth is allowed (default). Set the `max_depth` value to limit the depth level.

## Configuration

You can configure the following settings in the configuration file:

- `model_namespace`: Model namespace (default: `App\Models`)
- `resource_namespace`: Resource namespace (default: `App\Http\Resources`)
- `auto_load_relations`: Automatically load relations (default: `true`)
- `max_depth`: Maximum relation depth (default: `null` - infinite depth)
- `allowed_empty_collections`: Collection names to include even if empty

## Features

- ✅ Automatic relation resolution
- ✅ Model and Collection support
- ✅ Depth control (prevents infinite loops)
- ✅ Configurable namespaces
- ✅ Empty collection management

## License

MIT
