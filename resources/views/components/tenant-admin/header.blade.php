@props([
    'tenantQuery' => [],
    'tenantSlug' => null,
    'storeSettings' => null,
])

<header class="bg-white border-b border-slate-200 fixed top-0 w-full z-50 mb-16">
    <nav class="mx-auto max-w-7xl px-4 lg:px-8 py-3 flex items-center justify-between gap-4" aria-label="Global">
        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ route('tenant_admin.products.index', $tenantQuery) }}" class="flex items-center gap-2">
                @if (isset($storeSettings) && $storeSettings->logo_url)
                    <img src="{{ $storeSettings->logo_url }}" alt="Logo" class="h-8 w-auto">
                @else
                    <div class="h-8 w-8 rounded-lg text-white flex items-center justify-center font-bold text-sm"
                        style="background-color: var(--primary-color)">
                        {{ substr($tenantSlug ? ucfirst($tenantSlug) : config('app.name'), 0, 2) }}
                    </div>
                @endif
                <div class="hidden sm:block font-bold text-slate-900">
                    {{ $tenantSlug ? ucfirst($tenantSlug) : config('app.name') }}
                    <span class="font-normal text-slate-500 text-sm">Admin</span>
                </div>
            </a>
        </div>

        <div class="hidden md:flex items-center justify-center gap-4 text-sm/6 font-semibold text-slate-500">
            <a href="{{ route('tenant_admin.products.index', $tenantQuery) }}"
                class="{{ request()->routeIs('tenant_admin.products.*') ? 'text-slate-900' : 'hover:text-slate-900' }}">
                Produtos
            </a>
            <a href="{{ route('tenant_admin.categories.index', $tenantQuery) }}"
                class="{{ request()->routeIs('tenant_admin.categories.*') ? 'text-slate-900' : 'hover:text-slate-900' }}">
                Categorias
            </a>
            <a href="{{ route('tenant_admin.settings.edit', $tenantQuery) }}"
                class="{{ request()->routeIs('tenant_admin.settings.*') ? 'text-slate-900' : 'hover:text-slate-900' }}">
                Configurações
            </a>
        </div>

        <div class="flex items-center justify-end gap-3 flex-shrink-0">
            <button type="button" class="inline-flex items-center justify-center rounded-md p-2 text-slate-700 md:hidden"
                data-admin-drawer-open>
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="3" x2="21" y1="6" y2="6" />
                    <line x1="3" x2="21" y1="12" y2="12" />
                    <line x1="3" x2="21" y1="18" y2="18" />
                </svg>
            </button>
            <a href="{{ route('storefront.index', $tenantQuery) }}" target="_blank"
                class="hidden sm:block text-sm/6 font-semibold text-slate-900">
                Ver loja
                <span aria-hidden="true">&rarr;</span>
            </a>
            <form method="POST" action="{{ route('tenant_admin.logout', $tenantQuery) }}">
                @csrf
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />
                <button class="text-sm/6 font-semibold text-red-600 hover:text-red-500">Sair</button>
            </form>
        </div>
    </nav>

    <div class="md:hidden hidden" role="dialog" aria-modal="true" data-admin-drawer>
        <div class="fixed inset-0 z-40 bg-black/30" data-admin-drawer-close></div>
        <div class="fixed inset-y-0 left-0 z-50 w-full max-w-xs bg-white shadow-xl px-6 py-6 overflow-y-auto">
            <div class="flex items-center justify-between">
                <a href="{{ route('tenant_admin.products.index', $tenantQuery) }}" class="-m-1.5 p-1.5 flex items-center gap-2">
                    @if (isset($storeSettings) && $storeSettings->logo_url)
                        <img src="{{ $storeSettings->logo_url }}" alt="Logo" class="h-8 w-auto">
                    @else
                        <div class="h-8 w-8 rounded-lg text-white flex items-center justify-center font-bold text-sm"
                            style="background-color: var(--primary-color)">
                            {{ substr($tenantSlug ? ucfirst($tenantSlug) : config('app.name'), 0, 2) }}
                        </div>
                    @endif
                    <span class="font-bold text-slate-900 text-sm">
                        {{ $tenantSlug ? ucfirst($tenantSlug) : config('app.name') }}
                    </span>
                </a>
                <button type="button" class="-m-2.5 rounded-md p-2.5 text-slate-700" data-admin-drawer-close>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" x2="6" y1="6" y2="18" />
                        <line x1="6" x2="18" y1="6" y2="18" />
                    </svg>
                </button>
            </div>

            <div class="mt-6 space-y-1">
                <a href="{{ route('tenant_admin.products.index', $tenantQuery) }}"
                    class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('tenant_admin.products.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}">
                    Produtos
                </a>
                <a href="{{ route('tenant_admin.categories.index', $tenantQuery) }}"
                    class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('tenant_admin.categories.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}">
                    Categorias
                </a>
                <a href="{{ route('tenant_admin.settings.edit', $tenantQuery) }}"
                    class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('tenant_admin.settings.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}">
                    Configurações
                </a>
            </div>

            <div class="mt-6 space-y-2 border-t border-slate-100 pt-4">
                <a href="{{ route('storefront.index', $tenantQuery) }}" target="_blank"
                    class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Ver loja
                </a>
                <form method="POST" action="{{ route('tenant_admin.logout', $tenantQuery) }}">
                    @csrf
                    <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />
                    <button
                        class="w-full rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 text-left">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
