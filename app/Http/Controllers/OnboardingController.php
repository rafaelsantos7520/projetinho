<?php

namespace App\Http\Controllers;

use App\Tenancy\TenancyProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function create(): View
    {
        return view('onboarding.start');
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
            ->route('tenant_admin.login', ['tenant' => $tenant->slug])
            ->with('status', 'Loja criada. Agora entre com seu email e senha.');
    }
}

