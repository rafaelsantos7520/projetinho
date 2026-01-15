<?php

return [
    'base_domain' => env('TENANCY_BASE_DOMAIN', ''),
    'admin_subdomain_prefix' => env('TENANCY_ADMIN_SUBDOMAIN_PREFIX', 'admin'),
    'allow_header_and_query' => env('TENANCY_ALLOW_HEADER_AND_QUERY', false),
    'fallback_schema' => env('TENANCY_FALLBACK_SCHEMA', 'public'),
    'fallback_database' => env('TENANCY_FALLBACK_DATABASE', env('DB_LANDLORD_DATABASE', env('DB_DATABASE', 'laravel'))),
    'tenant_connection' => env('TENANCY_TENANT_CONNECTION', env('DB_CONNECTION', 'tenant')),
];
