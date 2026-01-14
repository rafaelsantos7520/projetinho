<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireTenant
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->bound(Tenant::class)) {
            return response()->view('tenancy.missing', status: 400);
        }

        $tenant = app(Tenant::class);

        // Validação extra de segurança: sessão deve pertencer ao tenant atual
        if (Auth::guard('web')->check()) {
            $sessionTenant = session('tenant_slug');
            // Se não tiver tenant na sessão (login antigo) ou for diferente
            if ($sessionTenant !== $tenant->slug) {
                Auth::guard('web')->logout();
                session()->invalidate();
                return redirect()->route('tenant_admin.login', ['tenant' => $tenant->slug]);
            }
        }

        return $next($request);
    }
}

