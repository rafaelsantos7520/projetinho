<x-layouts.app :title="'Loja'" :subtitle="'E-commerce'" :show-header="false" :full-width="true">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    {{-- Topbar --}}
    <div class="bg-slate-900 text-white text-xs py-2">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <p>Frete grátis para todo o Brasil nas compras acima de R$ 199</p>
            <div class="hidden sm:flex gap-4">
                <a href="#" class="hover:text-slate-300">Rastrear Pedido</a>
                <a href="#" class="hover:text-slate-300">Ajuda</a>
            </div>
        </div>
    </div>

    {{-- Header da Loja --}}
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center gap-8">
            {{-- Logo --}}
            <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" class="shrink-0">
                <div class="flex items-center gap-2">
                    <div
                        class="h-10 w-10 bg-slate-900 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($tenantSlug, 0, 1)) }}
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">{{ ucfirst($tenantSlug) }}</span>
                </div>
            </a>

            {{-- Busca --}}
            <form action="{{ route('storefront.index') }}" method="GET"
                class="hidden md:flex flex-1 max-w-lg relative group">
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}">
                <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Buscar produtos..."
                    class="w-full bg-slate-100 border-transparent focus:bg-white focus:border-slate-300 rounded-full py-2.5 pl-5 pr-12 text-sm transition-all outline-none ring-0">
                <button
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-slate-600 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </button>
            </form>

            {{-- Ações --}}
            <div class="flex items-center gap-4 ml-auto">
                <button class="md:hidden p-2 text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </button>
                <a href="#" class="p-2 text-slate-600 hover:text-slate-900 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </a>
                <a href="#" class="p-2 text-slate-600 hover:text-slate-900 relative group">
                    <span
                        class="absolute top-1 right-0 h-4 w-4 bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-white">0</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="8" cy="21" r="1" />
                        <circle cx="19" cy="21" r="1" />
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Menu Categorias (Mobile Scroll) --}}
        <div class="border-t border-slate-100 py-3 md:hidden overflow-x-auto">
            <div class="flex px-4 gap-3">
                @foreach ($categories as $cat)
                    <a href="{{ route('storefront.index', ['tenant' => $tenantSlug, 'category' => $cat->name]) }}"
                        class="whitespace-nowrap px-4 py-1.5 bg-slate-100 rounded-full text-xs font-medium text-slate-700">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </header>

    {{-- Hero Banner --}}
    <section class="relative h-[500px] w-full bg-slate-900 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop"
            alt="Hero" class="absolute inset-0 w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 h-full flex flex-col justify-center items-start pt-20">
            <span class="px-3 py-1 text-white text-xs font-bold uppercase tracking-wider rounded-full mb-4"
                style="background-color: var(--primary-color)">Nova
                Coleção</span>
            <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight max-w-2xl">
                Estilo que define você.
            </h1>
            <p class="text-lg text-slate-200 mb-8 max-w-xl">
                Descubra as tendências mais recentes com qualidade premium e preços imperdíveis.
            </p>
            <a href="#produtos"
                class="px-8 py-4 bg-white text-slate-900 font-bold rounded-full hover:bg-slate-100 transition-colors inline-flex items-center gap-2">
                Ver Ofertas
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                </svg>
            </a>
        </div>
    </section>

    {{-- Categorias Grid --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-slate-900 mb-8">Navegue por Categorias</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach ($categories->take(6) as $cat)
                    <a href="{{ route('storefront.index', ['tenant' => $tenantSlug, 'category' => $cat->name]) }}"
                        class="group flex flex-col items-center gap-3 text-center">
                        <div class="h-32 w-32 rounded-full bg-slate-100 overflow-hidden relative group-hover:ring-4 transition-all"
                            style="--tw-ring-color: var(--primary-color)">
                            <img src="{{ $cat->image_url ?? ('https://picsum.photos/seed/'.urlencode($cat->slug).'/200/200') }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <span
                            class="font-medium text-slate-700 group-hover:font-bold transition-all">{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Produtos --}}
    <section id="produtos" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Destaques da Semana</h2>
                    <p class="text-slate-500 mt-1">Os produtos mais desejados pelos nossos clientes.</p>
                </div>
                <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}"
                    class="text-sm font-semibold flex items-center gap-1 hover:opacity-80"
                    style="color: var(--primary-color)">
                    Ver tudo <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </a>
            </div>

            @if ($products->isEmpty())
                <div class="bg-white rounded-2xl p-12 text-center border border-dashed border-slate-300">
                    <div class="mx-auto h-12 w-12 text-slate-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900">Nenhum produto encontrado</h3>
                    <p class="text-slate-500 mt-1">Tente ajustar os filtros ou volte mais tarde.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-6">
                    @foreach ($products as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            @endif

            <div class="mt-12 text-center">
                <a href="#"
                    class="px-8 py-3 border border-slate-300 rounded-full font-medium text-slate-700 hover:bg-white hover:border-slate-400 transition-colors bg-white">
                    Carregar mais produtos
                </a>
            </div>
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="py-20 bg-slate-900 text-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Ganhe 10% de desconto</h2>
            <p class="text-slate-400 mb-8">Inscreva-se na nossa newsletter e receba ofertas exclusivas no seu e-mail.
            </p>
            <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                <input type="email" placeholder="Seu melhor e-mail"
                    class="flex-1 bg-white/10 border border-white/20 rounded-full px-5 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:ring-2"
                    style="--tw-ring-color: var(--primary-color)">
                <button class="px-8 py-3 text-white font-bold rounded-full hover:opacity-90 transition-opacity"
                    style="background-color: var(--primary-color)">
                    Inscrever
                </button>
            </form>
        </div>
    </section>

    {{-- Footer --}}
    <x-storefront.footer :tenant-slug="$tenantSlug" :store-settings="$storeSettings ?? null" />
</x-layouts.app>
