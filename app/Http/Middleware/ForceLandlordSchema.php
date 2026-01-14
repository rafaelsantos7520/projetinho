<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Tenancy\TenantSchemaManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceLandlordSchema
{
    public function __construct(
        private readonly TenantSchemaManager $schemaManager,
    ) {
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->schemaManager->resetToPublic();
        app()->forgetInstance(Tenant::class);

        return $next($request);
    }
}

