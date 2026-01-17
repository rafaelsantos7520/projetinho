@props([
    'fullWidth' => false,
    'shouldShowHeader' => true,
    'storeSettings' => null,
    'tenantSlug' => null,
    'hasTenant' => false,
    'subtitle' => null,
    'tenantQuery' => [],
    'isPlatformArea' => false,
])

@if ($shouldShowHeader)
<header
    class="{{ $fullWidth ? 'max-w-7xl mx-auto px-4 py-6' : 'flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8' }}">
    @if ($fullWidth)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    @endif

    <div class="flex items-center gap-3">
        @if (isset($storeSettings) && $storeSettings->logo_url)
            <img src="{{ $storeSettings->logo_url }}" alt="Logo" class="h-12 w-auto object-contain">
        @else
            <div class="h-10 w-10 rounded-xl text-white flex items-center justify-center font-bold text-lg"
                style="background-color: var(--primary-color)">
                {{ substr($tenantSlug ? ucfirst($tenantSlug) : config('app.name'), 0, 2) }}
            </div>
        @endif
        @if (!$hasTenant || (!isset($storeSettings->logo_url) || !$storeSettings->logo_url))
            <div>
                <div class="font-bold leading-tight">
                    {{ $tenantSlug ? ucfirst($tenantSlug) : config('app.name') }}</div>
                <div class="text-sm text-slate-500">{{ $subtitle ?? 'Loja Virtual' }}</div>
            </div>
        @endif
    </div>

    <nav class="flex flex-wrap items-center gap-2">
        @if (!request()->is('platform*') && $hasTenant)
            <a href="{{ route('storefront.index', $tenantQuery) }}"
                class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                Loja
            </a>
        @endif

        @if ($hasTenant)
            @if (\Illuminate\Support\Facades\Auth::check())
                <a href="{{ route('tenant_admin.products.index', $tenantQuery) }}"
                    class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                    Admin da Loja
                </a>
                <form method="POST" action="{{ route('tenant_admin.logout', $tenantQuery) }}">
                    @csrf
                    <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />
                    <button
                        class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-red-600">
                        Sair (Loja)
                    </button>
                </form>
            @else
                <a href="{{ route('tenant_admin.login', $tenantQuery) }}"
                    class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                    Entrar na Loja
                </a>
            @endif
        @endif

        @if ($isPlatformArea)
            @if (\Illuminate\Support\Facades\Auth::guard('platform')->check())
                <a href="{{ route('platform.tenants.index') }}"
                    class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                    Plataforma
                </a>
                <form method="POST" action="{{ route('platform.logout') }}">
                    @csrf
                    <button
                        class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                        Sair (Plat.)
                    </button>
                </form>
            @else
                <a href="{{ route('platform.login') }}"
                    class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                    Entrar (Plat.)
                </a>
                @if ($isPlatformArea || !$hasTenant)
                    <a href="{{ route('onboarding.create') }}"
                        class="text-sm px-4 py-2 rounded-2xl bg-primary text-white hover:bg-primary">
                        Criar loja
                    </a>
                @endif
            @endif
        @endif
    </nav>
    @if ($fullWidth)
</div>
@endif
</header>
@endif
