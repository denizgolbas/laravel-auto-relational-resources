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

### Example Response

When you use the package with loaded relations, the response will automatically include related resources:

**Controller:**
```php
$user = User::with(['posts', 'profile'])->find(1);
return new UserResource($user);
```

**Response:**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "posts": [
        {
            "id": 1,
            "title": "My First Post",
            "content": "This is my first post content",
            "comments": [
                {
                    "id": 1,
                    "body": "Great post!",
                    "author": {
                        "id": 2,
                        "name": "Jane Smith"
                    }
                }
            ]
        },
        {
            "id": 2,
            "title": "My Second Post",
            "content": "This is my second post content"
        }
    ],
    "profile": {
        "id": 1,
        "bio": "Software developer",
        "avatar": "https://example.com/avatar.jpg"
    },
    "meta": {
        "version": "1.0.0"
    }
}
```

As you can see, the package automatically:
- ✅ Includes the `posts` collection relation
- ✅ Includes the `profile` model relation
- ✅ Recursively includes nested relations like `comments` and `author`
- ✅ Uses the corresponding Resource classes (`PostResource`, `ProfileResource`, `CommentResource`, etc.)

### Collection Response Example

When using Resource Collections, relations are automatically resolved for each item:

**Controller:**
```php
$users = User::with(['posts', 'profile'])->get();
return UserResource::collection($users);
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "posts": [
                {
                    "id": 1,
                    "title": "My First Post",
                    "content": "This is my first post content"
                }
            ],
            "profile": {
                "id": 1,
                "bio": "Software developer"
            }
        },
        {
            "id": 2,
            "name": "Jane Smith",
            "email": "jane@example.com",
            "posts": [
                {
                    "id": 2,
                    "title": "Another Post",
                    "content": "Post content here"
                }
            ],
            "profile": {
                "id": 2,
                "bio": "Designer"
            }
        }
    ],
    "meta": {
        "version": "1.0.0"
    }
}
```

### Important Notes

**File Structure Requirement:** Collection and Resource files must follow the same path structure as your model files. The package converts the model class namespace to the Resource namespace to find the corresponding Resource class.

Examples:
- Model: `App\Models\User` → Resource: `App\Http\Resources\UserResource`
- Model: `App\Models\Product\Category` → Resource: `App\Http\Resources\Product\CategoryResource`
- Model: `App\Models\Order` → Collection: `App\Http\Resources\OrderCollection` (same rule applies for Collections)

If a Resource class is not found, the relation data will not be included.

**Depth Control:** By default, the package resolves all loaded relations with infinite depth. If you want to control the depth, you can use the `max_depth` configuration setting. If `max_depth` is null or 0, infinite depth is allowed (default). Set the `max_depth` value to limit the depth level.

## Configuration

After publishing the configuration file, you can customize the package behavior. The configuration file is located at `config/auto-relational-resources.php`.

### Available Configuration Options

#### `model_namespace`
**Default:** `App\Models`

The namespace where your Eloquent models are located. The package uses this to resolve Resource class names from Model class names.

**Example:**
```php
'model_namespace' => 'App\\Models',
// or
'model_namespace' => 'Domain\\Models',
```

#### `resource_namespace`
**Default:** `App\Http\Resources`

The namespace where your Resource classes are located. The package converts Model namespaces to Resource namespaces using this setting.

**Example:**
```php
'resource_namespace' => 'App\\Http\\Resources',
// or
'resource_namespace' => 'Domain\\Http\\Resources',
```

**How it works:**
- Model: `App\Models\User` → Resource: `App\Http\Resources\UserResource`
- Model: `Domain\Models\Product` → Resource: `Domain\Http\Resources\ProductResource`

#### `auto_load_relations`
**Default:** `true`

When set to `true`, relations are automatically merged into the resource response. Set to `false` to disable automatic relation loading.

**Example:**
```php
'auto_load_relations' => true, // Relations are automatically included
'auto_load_relations' => false, // Relations must be manually merged
```

#### `max_depth`
**Default:** `null` (infinite depth)

Controls the maximum depth level for resolving relations. Set to `null` or `0` for infinite depth (default). Use a positive integer to limit the depth.

**Examples:**
```php
'max_depth' => null,  // Infinite depth - all relations are resolved
'max_depth' => 0,     // Same as null - infinite depth
'max_depth' => 3,     // Maximum 3 levels deep
```

**Depth levels explained:**
- Depth 0: Main resource
- Depth 1: Direct relations of the main resource
- Depth 2: Relations of relations
- Depth 3: Relations of relations of relations

#### `allowed_empty_collections`
**Default:** `[]`

An array of collection relation names that should be included in the response even if they are empty. By default, empty collections are excluded.

**Example:**
```php
'allowed_empty_collections' => [
    'bankTransactionLines',
    'customerSlipLines',
],
```

This ensures that even if these collections are empty, they will appear in the response as empty arrays.

### Environment Variables

You can also configure these settings using environment variables in your `.env` file:

```env
AUTO_RELATIONAL_RESOURCES_MODEL_NAMESPACE=App\\Models
AUTO_RELATIONAL_RESOURCES_RESOURCE_NAMESPACE=App\\Http\\Resources
AUTO_RELATIONAL_RESOURCES_AUTO_LOAD=true
AUTO_RELATIONAL_RESOURCES_MAX_DEPTH=null
```

### Configuration Example

Here's a complete configuration example:

```php
return [
    'version' => '1.0.0',
    
    'auto_load_relations' => true,
    
    'max_depth' => null, // Infinite depth
    
    'model_namespace' => 'App\\Models',
    
    'resource_namespace' => 'App\\Http\\Resources',
    
    'allowed_empty_collections' => [
        'bankTransactionLines',
        'customerSlipLines',
    ],
];
```

## Features

- ✅ Automatic relation resolution
- ✅ Model and Collection support
- ✅ Depth control (prevents infinite loops)
- ✅ Configurable namespaces
- ✅ Empty collection management

## License

MIT
