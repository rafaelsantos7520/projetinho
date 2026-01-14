<?php

return [
    'base_domain' => env('TENANCY_BASE_DOMAIN', ''),
    'admin_subdomain_prefix' => env('TENANCY_ADMIN_SUBDOMAIN_PREFIX', 'admin'),
    'allow_header_and_query' => env('TENANCY_ALLOW_HEADER_AND_QUERY', false),
    'fallback_schema' => env('TENANCY_FALLBACK_SCHEMA', 'public'),
];
