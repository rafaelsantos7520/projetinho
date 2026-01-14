<?php

namespace App\Tenancy;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantResolver
{
    public function resolve(Request $request): ?Tenant
    {
        $host = $request->getHost();

        $allowHeaderAndQuery = app()->environment('local') || (bool) config('tenancy.allow_header_and_query', false);

        if ($allowHeaderAndQuery) {
            $header = trim((string) $request->header('X-Tenant', ''));
            if ($header !== '') {
                return Tenant::query()->where('slug', $header)->first();
            }

            $query = trim((string) $request->query('tenant', ''));
            if ($query !== '') {
                return Tenant::query()->where('slug', $query)->first();
            }

            $input = trim((string) $request->input('tenant', ''));
            if ($input !== '') {
                return Tenant::query()->where('slug', $input)->first();
            }
        }

        $tenantByDomain = Tenant::query()->where('domain', $host)->first();
        if ($tenantByDomain !== null) {
            return $tenantByDomain;
        }

        $slug = $this->resolveSlugFromSubdomain($host);
        if ($slug === null) {
            return null;
        }

        return Tenant::query()->where('slug', $slug)->first();
    }

    private function resolveSlugFromSubdomain(string $host): ?string
    {
        $baseDomain = trim((string) config('tenancy.base_domain', ''));
        if ($baseDomain === '' || ! Str::endsWith($host, '.' . $baseDomain)) {
            return null;
        }

        $subdomain = Str::before($host, '.' . $baseDomain);
        if ($subdomain === '') {
            return null;
        }

        $adminPrefix = trim((string) config('tenancy.admin_subdomain_prefix', ''));
        if ($adminPrefix !== '' && Str::startsWith($subdomain, $adminPrefix . '.')) {
            $subdomain = Str::after($subdomain, $adminPrefix . '.');
        }

        if ($subdomain === '' || Str::contains($subdomain, '.')) {
            return null;
        }

        return $subdomain;
    }
}
