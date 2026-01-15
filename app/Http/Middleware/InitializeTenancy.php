<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Tenancy\TenantResolver;
use App\Tenancy\TenantSchemaManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancy
{
    public function __construct(
        private readonly TenantResolver $resolver,
        private readonly TenantSchemaManager $schemaManager,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolver->resolve($request);

        if ($tenant instanceof Tenant) {
            $this->schemaManager->setSearchPath($tenant->schema);
            app()->instance(Tenant::class, $tenant);
            $this->isolateTenantSession($tenant->slug);
            URL::defaults(['tenant' => $tenant->slug]);
            config(['auth.defaults.guard' => 'web']);
            Auth::shouldUse('web');
        } else {
            $connectionName = (string) config('tenancy.tenant_connection', config('database.default'));
            $driver = DB::connection($connectionName)->getDriverName();
            $fallback = $driver === 'pgsql'
                ? (string) config('tenancy.fallback_schema', 'public')
                : (string) config('tenancy.fallback_database');

            $this->schemaManager->setSearchPath($fallback);
            config(['auth.defaults.guard' => 'platform']);
            Auth::shouldUse('platform');
        }

        return $next($request);
    }

    private function isolateTenantSession(string $tenantSlug): void
    {
        $baseCookie = (string) config('session.cookie');
        $suffix = 't_'.Str::of($tenantSlug)->lower()->replace([' ', '.'], '_');
        $cookie = rtrim($baseCookie, '_').'_'.(string) $suffix;

        config(['session.cookie' => $cookie]);
    }
}
