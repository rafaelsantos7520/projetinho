<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('tenant_admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt($credentials, remember: true)) {
            $request->session()->regenerate();
            $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

            if ($tenant) {
                $request->session()->put('tenant_slug', $tenant->slug);
            }

            return redirect()->route('tenant_admin.products.index', $tenant ? ['tenant' => $tenant->slug] : []);
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

        return redirect()->route('tenant_admin.login', $tenant ? ['tenant' => $tenant->slug] : []);
    }
}
