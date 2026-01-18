<x-layouts.app :title="'Produtos'" :subtitle="'E-commerce'" :show-header="false" :full-width="true">
    @php
        $tenantSlug = app(\App\Models\Tenant::class)->slug;
    @endphp

    <div x-data="{ scrolled: false, searchOpen: false, filtersOpen: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
    {{-- Topbar --}}
    <div class="bg-primary-strong text-white text-xs py-2.5 font-medium tracking-wide">
        <div class="max-w-7xl mx-auto px-4 flex justify-center sm:justify-between items-center text-center sm:text-left">
            <p class="hidden sm:block">Enviamos para todo o Brasil com segurança</p>
            <p class="flex items-center gap-2 justify-center w-full sm:w-auto">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                Frete grátis nas compras acima de R$ 199,00
            </p>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 shadow-sm transition-all text-slate-900">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between gap-8">
            {{-- Logo --}}
            <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" class="shrink-0 group relative z-10">
                <div class="flex items-center gap-3">
                     @if(isset($storeSettings) && $storeSettings->logo_url)
                        <img src="{{ $storeSettings->logo_url }}" alt="Logo" class="h-12 w-auto object-contain transition-transform group-hover:scale-105 drop-shadow-sm">
                    @else
                        <div class="h-10 w-10 bg-primary rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-slate-900/20 group-hover:shadow-slate-900/40 transition-all rotate-3 group-hover:rotate-0">
                            {{ strtoupper(substr($tenantSlug, 0, 1)) }}
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-primary transition-colors hidden sm:block">{{ ucfirst($tenantSlug) }}</span>
                    @endif
                </div>
            </a>

            {{-- Busca Desktop --}}
            <form action="{{ route('storefront.products', ['tenant' => $tenantSlug]) }}" method="GET" class="hidden md:flex flex-1 max-w-lg relative group">
                <div class="relative w-full">
                    <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Buscar produtos..."
                        class="w-full bg-slate-100/50 border border-slate-200 focus:bg-white focus:border-slate-300 rounded-full py-2.5 pl-12 pr-4 text-sm transition-all outline-none ring-0 placeholder:text-slate-400 group-hover:bg-white shadow-sm group-hover:shadow-md">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>

            {{-- Ações Mobile --}}
            <div class="flex items-center gap-3 md:hidden">
                 <button @click="searchOpen = !searchOpen" class="p-2 text-slate-600 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                 </button>
                 <button @click="filtersOpen = !filtersOpen" class="p-2 text-slate-600 hover:text-slate-900 relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    @if(!empty($filters['category']) || !empty($filters['min_rating']) || $filters['sort'] !== 'newest')
                        <span class="absolute -top-1 -right-1 h-3 w-3 bg-primary rounded-full"></span>
                    @endif
                 </button>
            </div>

            {{-- Ações Desktop --}}
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" class="text-sm font-medium text-slate-600 hover:text-primary transition-colors">
                    Início
                </a>
                <a href="{{ route('storefront.products', ['tenant' => $tenantSlug]) }}" class="text-sm font-medium text-primary">
                    Produtos
                </a>
                <span class="h-4 w-px bg-slate-200"></span>
                <a href="#" class="group relative p-2" aria-label="Carrinho">
                    <span class="absolute -top-1 -right-1 h-5 w-5 bg-primary text-white text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">0</span>
                    <svg class="w-6 h-6 text-slate-600 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </a>
            </div>
        </div>

        {{-- Mobile Search Overlay --}}
        <div x-show="searchOpen" x-transition class="md:hidden absolute top-full left-0 w-full bg-white border-b border-slate-100 p-4 shadow-lg z-0">
             <form action="{{ route('storefront.products', ['tenant' => $tenantSlug]) }}" method="GET" class="relative">
                <input name="q" placeholder="O que você procura?" class="w-full bg-slate-50 border border-slate-200 rounded-lg py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-slate-300">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>
    </header>

    <main class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-50">
        {{-- Breadcrumb --}}
        <div class="bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <nav class="flex items-center gap-2 text-sm text-slate-500">
                    <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" class="hover:text-primary transition-colors">Início</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-slate-900 font-medium">
                        @if($selectedCategory)
                            {{ $selectedCategory->name }}
                        @elseif(!empty($filters['q']))
                            Resultados para "{{ $filters['q'] }}"
                        @else
                            Todos os Produtos
                        @endif
                    </span>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Sidebar Filtros (Desktop) --}}
                <aside class="hidden lg:block w-72 shrink-0">
                    <div class="sticky top-28 space-y-6">
                        {{-- Categorias --}}
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                            <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                Categorias
                            </h3>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug], array_filter(['q' => $filters['q'], 'sort' => $filters['sort']]))) }}" 
                                       class="flex items-center justify-between py-2 px-3 rounded-lg transition-colors {{ empty($filters['category']) ? 'bg-primary/10 text-primary font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <span>Todas</span>
                                        <span class="text-xs text-slate-400">{{ $products->total() }}</span>
                                    </a>
                                </li>
                                @foreach($categories as $cat)
                                    <li>
                                        <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug, 'category' => $cat->slug ?? $cat->name], array_filter(['q' => $filters['q'], 'sort' => $filters['sort']]))) }}" 
                                           class="flex items-center justify-between py-2 px-3 rounded-lg transition-colors {{ ($filters['category'] == $cat->slug || $filters['category'] == $cat->name) ? 'bg-primary/10 text-primary font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                            <span>{{ $cat->name }}</span>
                                            <span class="text-xs text-slate-400">{{ $cat->products_count ?? 0 }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Filtro por Avaliação --}}
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                            <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Avaliação
                            </h3>
                            <div class="space-y-2">
                                @foreach([4, 3, 2, 1] as $rating)
                                    <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug, 'min_rating' => $rating], array_filter(['q' => $filters['q'], 'category' => $filters['category'], 'sort' => $filters['sort']]))) }}" 
                                       class="flex items-center gap-2 py-2 px-3 rounded-lg transition-colors {{ $filters['min_rating'] == $rating ? 'bg-primary/10 text-primary font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm">ou mais</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Limpar Filtros --}}
                        @if(!empty($filters['category']) || !empty($filters['min_rating']) || !empty($filters['q']))
                            <a href="{{ route('storefront.products', ['tenant' => $tenantSlug]) }}" 
                               class="block w-full text-center py-3 px-4 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                                Limpar Filtros
                            </a>
                        @endif
                    </div>
                </aside>

                {{-- Conteúdo Principal --}}
                <div class="flex-1 min-w-0">
                    {{-- Header de Resultados --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">
                                @if($selectedCategory)
                                    {{ $selectedCategory->name }}
                                @elseif(!empty($filters['q']))
                                    Resultados para "{{ $filters['q'] }}"
                                @else
                                    Todos os Produtos
                                @endif
                            </h1>
                            <p class="text-slate-500 text-sm mt-1">{{ $products->total() }} produto{{ $products->total() !== 1 ? 's' : '' }} encontrado{{ $products->total() !== 1 ? 's' : '' }}</p>
                        </div>

                        {{-- Ordenação --}}
                        <div class="flex items-center gap-3">
                            <label class="text-sm text-slate-500">Ordenar por:</label>
                            <select onchange="window.location = this.value" 
                                    class="bg-white border border-slate-200 rounded-lg py-2 px-4 text-sm font-medium text-slate-700 focus:outline-none focus:border-primary cursor-pointer">
                                @php
                                    $sortOptions = [
                                        'newest' => 'Mais Recentes',
                                        'price_asc' => 'Menor Preço',
                                        'price_desc' => 'Maior Preço',
                                        'rating_desc' => 'Melhor Avaliação',
                                        'name_asc' => 'Nome A-Z',
                                    ];
                                @endphp
                                @foreach($sortOptions as $value => $label)
                                    <option value="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug, 'sort' => $value], array_filter(['q' => $filters['q'], 'category' => $filters['category'], 'min_rating' => $filters['min_rating']]))) }}" 
                                            {{ $filters['sort'] === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Filtros Ativos --}}
                    @if(!empty($filters['category']) || !empty($filters['min_rating']) || !empty($filters['q']))
                        <div class="flex flex-wrap gap-2 mb-6">
                            @if(!empty($filters['q']))
                                <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug], array_filter(['category' => $filters['category'], 'min_rating' => $filters['min_rating'], 'sort' => $filters['sort']]))) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-sm font-medium rounded-full hover:bg-primary/20 transition-colors">
                                    Busca: {{ $filters['q'] }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                            @if(!empty($filters['category']))
                                <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug], array_filter(['q' => $filters['q'], 'min_rating' => $filters['min_rating'], 'sort' => $filters['sort']]))) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-sm font-medium rounded-full hover:bg-primary/20 transition-colors">
                                    {{ $selectedCategory->name ?? $filters['category'] }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                            @if(!empty($filters['min_rating']))
                                <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug], array_filter(['q' => $filters['q'], 'category' => $filters['category'], 'sort' => $filters['sort']]))) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-sm font-medium rounded-full hover:bg-primary/20 transition-colors">
                                    {{ $filters['min_rating'] }}+ estrelas
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Grid de Produtos --}}
                    @if($products->isEmpty())
                        <div class="bg-white rounded-3xl p-16 text-center border border-dashed border-slate-200">
                            <div class="mx-auto h-20 w-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Nenhum produto encontrado</h3>
                            <p class="text-slate-500 mb-6">Tente ajustar os filtros ou realizar uma nova busca.</p>
                            <a href="{{ route('storefront.products', ['tenant' => $tenantSlug]) }}" class="inline-block px-6 py-3 bg-primary text-white font-medium rounded-xl hover:bg-primary/90 transition-colors">
                                Ver Todos os Produtos
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                @include('storefront.partials.product-card', ['product' => $product])
                            @endforeach
                        </div>

                        {{-- Paginação --}}
                        @if($products->hasPages())
                            <div class="mt-12">
                                {{ $products->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Mobile Filters Drawer --}}
        <div x-show="filtersOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
             @click="filtersOpen = false">
        </div>
        <div x-show="filtersOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="lg:hidden fixed right-0 top-0 bottom-0 z-50 w-80 max-w-full bg-white shadow-2xl overflow-y-auto"
             @click.stop>
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-slate-900">Filtros</h2>
                    <button @click="filtersOpen = false" class="p-2 text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Categorias Mobile --}}
                <div class="mb-6">
                    <h3 class="font-bold text-slate-900 mb-3">Categorias</h3>
                    <div class="space-y-1">
                        <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug], array_filter(['q' => $filters['q'], 'sort' => $filters['sort']]))) }}" 
                           class="block py-2 px-3 rounded-lg {{ empty($filters['category']) ? 'bg-primary/10 text-primary font-medium' : 'text-slate-600' }}">
                            Todas as Categorias
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug, 'category' => $cat->slug ?? $cat->name], array_filter(['q' => $filters['q'], 'sort' => $filters['sort']]))) }}" 
                               class="block py-2 px-3 rounded-lg {{ ($filters['category'] == $cat->slug || $filters['category'] == $cat->name) ? 'bg-primary/10 text-primary font-medium' : 'text-slate-600' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Avaliação Mobile --}}
                <div class="mb-6">
                    <h3 class="font-bold text-slate-900 mb-3">Avaliação Mínima</h3>
                    <div class="space-y-1">
                        @foreach([4, 3, 2, 1] as $rating)
                            <a href="{{ route('storefront.products', array_merge(['tenant' => $tenantSlug, 'min_rating' => $rating], array_filter(['q' => $filters['q'], 'category' => $filters['category'], 'sort' => $filters['sort']]))) }}" 
                               class="flex items-center gap-2 py-2 px-3 rounded-lg {{ $filters['min_rating'] == $rating ? 'bg-primary/10 text-primary font-medium' : 'text-slate-600' }}">
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-sm">ou mais</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Limpar Filtros Mobile --}}
                @if(!empty($filters['category']) || !empty($filters['min_rating']) || !empty($filters['q']))
                    <a href="{{ route('storefront.products', ['tenant' => $tenantSlug]) }}" 
                       class="block w-full text-center py-3 px-4 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                        Limpar Todos os Filtros
                    </a>
                @endif
            </div>
        </div>
    </main>

    <x-storefront.footer :tenant-slug="$tenantSlug" :store-settings="$storeSettings" />
    </div>
</x-layouts.app>
