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

Route::get('/login', function (Request $request) {
    $intended = session()->get('url.intended');
    $tenantParam = [];

    // Tentar recuperar tenant da request atual
    if ($request->has('tenant')) {
        $tenantParam['tenant'] = $request->query('tenant');
    }

    if (is_string($intended) && $intended !== '') {
        $parts = parse_url($intended);
        $path = $parts['path'] ?? '';
        parse_str($parts['query'] ?? '', $query);

        // Se encontrou tenant na url intended, usa ele prioritariamente
        if (isset($query['tenant']) && ! empty($query['tenant'])) {
            $tenantParam['tenant'] = $query['tenant'];
        }

        if (str_starts_with($path, '/platform')) {
            return redirect()->route('platform.login');
        }

        // Se for admin e tivermos o tenant, redireciona direto
        if (str_starts_with($path, '/admin') && ! empty($tenantParam)) {
            return redirect()->route('tenant_admin.login', $tenantParam);
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

Route::get('/loja', [StorefrontController::class, 'index'])->middleware(RequireTenant::class)->name('storefront.index');
Route::get('/loja/produto/{product}', [StorefrontController::class, 'show'])->middleware(RequireTenant::class)->name('storefront.product');

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

    Route::get('/settings', [TenantAdminSettingsController::class, 'edit'])->middleware('auth')->name('settings.edit');
    Route::put('/settings', [TenantAdminSettingsController::class, 'update'])->middleware('auth')->name('settings.update');

    Route::get('/products', [TenantAdminProductController::class, 'index'])->middleware('auth')->name('products.index');
    Route::get('/products/create', [TenantAdminProductController::class, 'create'])->middleware('auth')->name('products.create');
    Route::post('/products', [TenantAdminProductController::class, 'store'])->middleware('auth')->name('products.store');
    Route::get('/products/{product}/edit', [TenantAdminProductController::class, 'edit'])->middleware('auth')->name('products.edit');
    Route::put('/products/{product}', [TenantAdminProductController::class, 'update'])->middleware('auth')->name('products.update');
    Route::delete('/products/{product}', [TenantAdminProductController::class, 'destroy'])->middleware('auth')->name('products.destroy');

    Route::resource('categories', TenantAdminCategoryController::class)->middleware('auth');
});

Route::prefix('platform')->name('platform.')->middleware(ForceLandlordSchema::class)->group(function () {
    Route::get('/login', [PlatformAuthController::class, 'create'])->middleware('guest:platform')->name('login');
    Route::post('/login', [PlatformAuthController::class, 'store'])->middleware('guest:platform')->name('login.store');
    Route::post('/logout', [PlatformAuthController::class, 'destroy'])->middleware('auth:platform')->name('logout');

    Route::get('/tenants', [PlatformTenantController::class, 'index'])->middleware('auth:platform')->name('tenants.index');
    Route::get('/tenants/create', [PlatformTenantController::class, 'create'])->middleware('auth:platform')->name('tenants.create');
    Route::post('/tenants', [PlatformTenantController::class, 'store'])->middleware('auth:platform')->name('tenants.store');
});
