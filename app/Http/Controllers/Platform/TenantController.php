<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Tenancy\TenancyProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::query()->orderBy('created_at', 'desc')->get();

        return view('platform.tenants.index', [
            'tenants' => $tenants,
        ]);
    }

    public function create(): View
    {
        return view('platform.tenants.create');
    }

    public function store(Request $request, TenancyProvisioner $provisioner): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9\\-_]+$/'],
            'domain' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:120'],
            'owner_email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $tenant = $provisioner->createTenant(
            slug: $validated['slug'],
            domain: $validated['domain'] ?? null,
            ownerEmail: $validated['owner_email'],
            ownerPassword: $validated['password'],
            ownerName: $validated['owner_name'],
        );

        return redirect()
            ->route('platform.tenants.create')
            ->with('created_tenant_slug', $tenant->slug);
    }
}
