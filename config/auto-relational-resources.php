<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto Relational Resources Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya paket için yapılandırma ayarlarını içerir.
    |
    */

    'version' => env('AUTO_RELATIONAL_RESOURCES_VERSION', '1.0.0'),

    'auto_load_relations' => env('AUTO_RELATIONAL_RESOURCES_AUTO_LOAD', true),

    /*
    |--------------------------------------------------------------------------
    | Maximum Depth
    |--------------------------------------------------------------------------
    |
    | Maksimum ilişki derinliği. null veya 0 değeri sonsuz depth'e izin verir.
    | Varsayılan: null (sonsuz depth)
    |
    */

    'max_depth' => env('AUTO_RELATIONAL_RESOURCES_MAX_DEPTH', null),

    /*
    |--------------------------------------------------------------------------
    | Model and Resource Namespaces
    |--------------------------------------------------------------------------
    |
    | Model ve Resource namespace'lerini belirleyin.
    |
    */

    'model_namespace' => env('AUTO_RELATIONAL_RESOURCES_MODEL_NAMESPACE', 'App\\Models'),

    'resource_namespace' => env('AUTO_RELATIONAL_RESOURCES_RESOURCE_NAMESPACE', 'App\\Http\\Resources'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Empty Collections
    |--------------------------------------------------------------------------
    |
    | Boş olsa bile dahil edilecek collection isimleri.
    |
    */

    'allowed_empty_collections' => [],
];

