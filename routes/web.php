<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Platform\AuthController as PlatformAuthController;
use App\Http\Controllers\Platform\TenantController as PlatformTenantController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\TenantAdmin\AuthController as TenantAdminAuthController;
use App\Http\Controllers\TenantAdmin\CategoryController as TenantAdminCategoryController;
use App\Http\Controllers\TenantAdmin\ProductController as TenantAdminProductController;
use App\Http\Controllers\TenantAdmin\SettingsController as TenantAdminSettingsController;
use App\Http\Middleware\ForceLandlordSchema;
use App\Http\Middleware\RequireTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$baseDomain = config('tenancy.base_domain');

/*
|--------------------------------------------------------------------------
| Rotas Compartilhadas do Tenant
|--------------------------------------------------------------------------
|
| Estas rotas são usadas tanto no modo subdomínio quanto no modo path.
|
*/
$tenantRoutes = function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');
    Route::get('/produto/{product}', [StorefrontController::class, 'show'])->name('storefront.product');

    // Redirecionamento de compatibilidade
    Route::get('/loja', function () {
        return redirect()->route('storefront.index');
    });
    Route::get('/loja/produto/{product}', function ($product) {
        return redirect()->route('storefront.product', $product);
    });

    Route::prefix('admin')->name('tenant_admin.')->group(function () {
        Route::get('/redirect', function (Request $request) {
            if (Auth::guard('web')->check()) {
                return redirect()->route('tenant_admin.products.index');
            }
            return redirect()->route('tenant_admin.login');
        })->name('redirect');

        Route::get('/login', [TenantAdminAuthController::class, 'create'])->middleware('guest')->name('login');
        Route::post('/login', [TenantAdminAuthController::class, 'store'])->middleware('guest')->name('login.store');
        Route::post('/logout', [TenantAdminAuthController::class, 'destroy'])->middleware('auth')->name('logout');

        Route::middleware('auth')->group(function () {
            Route::get('/settings', [TenantAdminSettingsController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [TenantAdminSettingsController::class, 'update'])->name('settings.update');

            Route::get('/products', [TenantAdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [TenantAdminProductController::class, 'create'])->name('products.create');
            Route::post('/products', [TenantAdminProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [TenantAdminProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [TenantAdminProductController::class, 'update'])->name('products.update');
            Route::post('/products/{product}/duplicate', [TenantAdminProductController::class, 'duplicate'])->name('products.duplicate');
            Route::delete('/products/{product}', [TenantAdminProductController::class, 'destroy'])->name('products.destroy');

            Route::resource('categories', TenantAdminCategoryController::class);
        });
    });
};

/*
|--------------------------------------------------------------------------
| Definição de Rotas
|--------------------------------------------------------------------------
*/

if ($baseDomain) {
    // =========================================================================
    // MODO SUBDOMÍNIO (ex: loja.meudominio.com)
    // =========================================================================

    // Rotas do Tenant
    Route::domain('{tenant}.' . $baseDomain)
        ->middleware(RequireTenant::class)
        ->group($tenantRoutes);

    // Rotas do Landlord (Domínio Principal)
    Route::domain($baseDomain)->group(function () use ($baseDomain) {

        // Autenticação Global
        Route::get('/login', function (Request $request) {
            $intended = session()->get('url.intended');
            // No modo domínio, o tenant já deve vir pela URL, mas mantemos lógica defensiva
            return view('auth.select', ['tenantParam' => []]);
        })->name('login');

        Route::middleware(ForceLandlordSchema::class)->group(function () use ($baseDomain) {
            // ROTA TEMPORÁRIA PARA RESET DE SENHA - DELETE APÓS USAR
            Route::get('/reset-admin', function () {
                $user = \App\Models\PlatformUser::where('email', 'admin@site.com')->first();
                if ($user) {
                    $user->password = 'admin123';
                    $user->save();
                    return "Senha do admin@site.com resetada para: admin123";
                }
                return "Usuário admin@site.com não encontrado.";
            });

            Route::get('/start', [OnboardingController::class, 'create'])->name('onboarding.create');
            Route::post('/start', [OnboardingController::class, 'store'])->name('onboarding.store');

            Route::get('/', function (Request $request) use ($baseDomain) {
                // Redirecionamento inteligente: Se acessar raiz com ?tenant=x, vai para x.dominio.com
                $tenant = trim((string) $request->query('tenant', ''));
                if ($tenant !== '') {
                    $protocol = $request->secure() ? 'https://' : 'http://';
                    return redirect()->away($protocol . $tenant . '.' . $baseDomain);
                }

                if (Auth::guard('platform')->check()) {
                    return redirect()->route('platform.tenants.index');
                }
                return redirect()->route('platform.login');
            })->name('root');

            Route::prefix('platform')->name('platform.')->group(function () {
                // Registro de novo admin (Aberto temporariamente para o primeiro acesso)
                Route::get('/register', [PlatformAuthController::class, 'showRegister'])->name('register');
                Route::post('/register', [PlatformAuthController::class, 'register'])->name('register.store');

                Route::get('/login', [PlatformAuthController::class, 'create'])->middleware('guest:platform')->name('login');
                Route::post('/login', [PlatformAuthController::class, 'store'])->middleware('guest:platform')->name('login.store');
                Route::post('/logout', [PlatformAuthController::class, 'destroy'])->middleware('auth:platform')->name('logout');

                Route::get('/tenants', [PlatformTenantController::class, 'index'])->middleware('auth:platform')->name('tenants.index');
                Route::get('/tenants/create', [PlatformTenantController::class, 'create'])->middleware('auth:platform')->name('tenants.create');
                Route::post('/tenants', [PlatformTenantController::class, 'store'])->middleware('auth:platform')->name('tenants.store');
            });
        });
    });
} else {
    // =========================================================================
    // MODO PATH (ex: meudominio.com/loja?tenant=x) - LEGADO
    // =========================================================================

    Route::get('/login', function (Request $request) {
        $intended = session()->get('url.intended');
        $tenantParam = [];
        if ($request->has('tenant')) {
            $tenantParam['tenant'] = $request->query('tenant');
        }
        if (is_string($intended) && $intended !== '') {
            $parts = parse_url($intended);
            parse_str($parts['query'] ?? '', $query);
            if (isset($query['tenant']) && ! empty($query['tenant'])) {
                $tenantParam['tenant'] = $query['tenant'];
            }
        }
        return view('auth.select', ['tenantParam' => $tenantParam]);
    })->name('login');

    Route::middleware(ForceLandlordSchema::class)->group(function () {
        Route::get('/start', [OnboardingController::class, 'create'])->name('onboarding.create');
        Route::post('/start', [OnboardingController::class, 'store'])->name('onboarding.store');
    });

    Route::get('/', function (Request $request) {
        $tenant = trim((string) $request->query('tenant', ''));
        if ($tenant !== '') {
            $query = $request->query();
            unset($query['tenant']);
            return redirect()->route('storefront.index', ['tenant' => $tenant] + $query);
        }
        if (Auth::guard('platform')->check()) {
            return redirect()->route('platform.tenants.index');
        }
        return redirect()->route('platform.login');
    })->middleware(ForceLandlordSchema::class)->name('root');

    // Rotas manuais para Storefront (Path based)
    Route::get('/loja', [StorefrontController::class, 'index'])->middleware(RequireTenant::class)->name('storefront.index');
    Route::get('/loja/produto/{product}', [StorefrontController::class, 'show'])->middleware(RequireTenant::class)->name('storefront.product');

    // Rotas Admin
    Route::prefix('admin')->name('tenant_admin.')->middleware(RequireTenant::class)->group(function () {
        Route::get('/redirect', function (Request $request) {
            $tenant = $request->query('tenant');
            $params = $tenant ? ['tenant' => $tenant] : [];
            if (Auth::guard('web')->check()) {
                return redirect()->route('tenant_admin.products.index', $params);
            }
            return redirect()->route('tenant_admin.login', $params);
        })->name('redirect');

        Route::get('/login', [TenantAdminAuthController::class, 'create'])->middleware('guest')->name('login');
        Route::post('/login', [TenantAdminAuthController::class, 'store'])->middleware('guest')->name('login.store');
        Route::post('/logout', [TenantAdminAuthController::class, 'destroy'])->middleware('auth')->name('logout');

        Route::middleware('auth')->group(function () {
            Route::get('/settings', [TenantAdminSettingsController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [TenantAdminSettingsController::class, 'update'])->name('settings.update');

            Route::get('/products', [TenantAdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [TenantAdminProductController::class, 'create'])->name('products.create');
            Route::post('/products', [TenantAdminProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [TenantAdminProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [TenantAdminProductController::class, 'update'])->name('products.update');
            Route::post('/products/{product}/duplicate', [TenantAdminProductController::class, 'duplicate'])->name('products.duplicate');
            Route::delete('/products/{product}', [TenantAdminProductController::class, 'destroy'])->name('products.destroy');

            Route::resource('categories', TenantAdminCategoryController::class);
        });
    });

    Route::prefix('platform')->name('platform.')->middleware(ForceLandlordSchema::class)->group(function () {
        Route::get('/login', [PlatformAuthController::class, 'create'])->middleware('guest:platform')->name('login');
        Route::post('/login', [PlatformAuthController::class, 'store'])->middleware('guest:platform')->name('login.store');
        Route::post('/logout', [PlatformAuthController::class, 'destroy'])->middleware('auth:platform')->name('logout');

        Route::get('/tenants', [PlatformTenantController::class, 'index'])->middleware('auth:platform')->name('tenants.index');
        Route::get('/tenants/create', [PlatformTenantController::class, 'create'])->middleware('auth:platform')->name('tenants.create');
        Route::post('/tenants', [PlatformTenantController::class, 'store'])->middleware('auth:platform')->name('tenants.store');
    });
}
