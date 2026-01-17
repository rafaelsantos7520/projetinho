<x-layouts.app :title="'Loja'" :subtitle="'E-commerce'" :show-header="false" :full-width="true">
    @php
        $tenantSlug = app(\App\Models\Tenant::class)->slug;
        $banners = array_values(array_filter([
            $storeSettings->banner_1_url,
            $storeSettings->banner_2_url,
            $storeSettings->banner_3_url,
        ]));
        $bio = $storeSettings->biography;
        $hasBio = !empty($bio);
        $displayBio = $hasBio ? $bio : "Bem-vindo à nossa loja! Somos apaixonados por oferecer os melhores produtos com qualidade e estilo. Nossa missão é proporcionar uma experiência de compra incrível, com peças selecionadas especialmente para você. Explore nossa coleção e descubra o que preparamos.";
    @endphp

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
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 shadow-sm transition-all text-slate-900" 
            x-data="{ scrolled: false, searchOpen: false }" 
            @scroll.window="scrolled = (window.pageYOffset > 20)">
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
            <form action="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" method="GET" class="hidden md:flex flex-1 max-w-lg relative group">
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}">
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
            </div>

            {{-- Ações Desktop --}}
            <div class="hidden md:flex items-center gap-4">
                <a href="#" class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Entrar</span>
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
             <form action="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" method="GET" class="relative">
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}">
                <input name="q" placeholder="O que você procura?" class="w-full bg-slate-50 border border-slate-200 rounded-lg py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-slate-300">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>
    </header>

    <main class="min-h-screen pb-20 bg-gradient-to-b from-slate-50 via-white to-slate-50">

        {{-- Hero Carousel --}}
        @if(count($banners) > 0)
            <section class="relative h-[500px] md:h-[600px] w-full bg-slate-900 overflow-hidden group" 
                     x-data="{ active: 0, slides: {{ json_encode($banners) }}, timer: null }"
                     x-init="timer = setInterval(() => active = (active + 1) % slides.length, 5000)">
                
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="active === index"
                         x-transition:enter="transition-opacity ease-linear duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-linear duration-500"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0">
                        <img :src="slide" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30"></div>
                    </div>
                </template>

                <div class="absolute inset-0 flex items-center justify-between p-4 mix-blend-overlay opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="active = (active - 1 + slides.length) % slides.length; clearInterval(timer)" class="p-3 rounded-full bg-white/10 hover:bg-white/20 text-white backdrop-blur-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="active = (active + 1) % slides.length; clearInterval(timer)" class="p-3 rounded-full bg-white/10 hover:bg-white/20 text-white backdrop-blur-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2">
                    <template x-for="(_, index) in slides" :key="index">
                        <button @click="active = index; clearInterval(timer)" 
                            :class="active === index ? 'w-8 bg-white' : 'w-2 bg-white/50'"
                            class="h-2 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </section>
        @else
            {{-- Fallback Hero --}}
            <section class="relative h-[550px] w-full bg-slate-900 overflow-hidden flex items-center justify-center">
                <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=2070&auto=format&fit=crop"
                    alt="Hero" class="absolute inset-0 w-full h-full object-cover opacity-60 scale-105 animate-slow-zoom">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                
                <div class="relative max-w-4xl mx-auto px-4 text-center text-white space-y-8 animate-fade-in-up">
                    <span class="inline-block border border-white/30 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-[0.2em] mb-4">
                        Nova Coleção
                    </span>
                    <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-none drop-shadow-2xl">
                        Estilo que define <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">quem você é.</span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto font-light leading-relaxed">
                        Explore nossa seleção exclusiva de produtos que combinam qualidade premium e design inconfundível.
                    </p>
                    <div class="pt-4 flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="#produtos" class="px-8 py-4 bg-primary text-white font-bold rounded-full hover:bg-primary transition-all shadow-[0_10px_50px_rgba(0,0,0,0.35)] hover:shadow-[0_16px_60px_rgba(0,0,0,0.45)] hover:-translate-y-1 active:scale-[0.98]">
                            Ver Produtos
                        </a>
                        <a href="#" class="px-8 py-4 bg-white/10 border border-white/30 text-white font-bold rounded-full hover:bg-white/20 transition-all hover:-translate-y-1 active:scale-[0.98] backdrop-blur-sm">
                            Sobre a Marca
                        </a>
                    </div>
                </div>
            </section>
        @endif

        {{-- Features Strip --}}
        <section class="relative -mt-12 md:-mt-16 z-10">
            <div class="max-w-7xl mx-auto px-4">
                <div class="bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-200 shadow-2xl shadow-slate-900/10">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-6 py-6 md:px-10 md:py-8">
                        <div class="flex items-center gap-4 justify-center md:justify-start group">
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-600 group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Produtos Premium</h3>
                                <p class="text-sm text-slate-500">Qualidade garantida em cada detalhe</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 justify-center md:justify-start group">
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-600 group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Entrega Rápida</h3>
                                <p class="text-sm text-slate-500">Receba seu pedido em tempo recorde</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 justify-center md:justify-start group">
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-600 group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Compra Segura</h3>
                                <p class="text-sm text-slate-500">Seus dados protegidos de ponta a ponta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- About / Bio Section (Always Shown) --}}
        <section class="py-16 md:py-20">
            <div class="max-w-5xl mx-auto px-4">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-900/5 p-8 md:p-12 text-center space-y-8">
                 <div class="inline-flex items-center justify-center p-3 bg-slate-50 rounded-2xl shadow-sm mb-2 transform rotate-2 hover:rotate-0 transition-transform duration-300">
                     @if($storeSettings->logo_url)
                        <img src="{{ $storeSettings->logo_url }}" class="h-16 w-auto object-contain md:h-20">
                    @else
                        <span class="text-2xl font-bold text-slate-900 px-4">{{ strtoupper(substr($tenantSlug, 0, 2)) }}</span>
                    @endif
                 </div>
                 
                 <div>
                     <h2 class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Nossa História</h2>
                     <h3 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Bem-vindo à {{ ucfirst($tenantSlug) }}</h3>
                     <div class="prose prose-slate prose-lg mx-auto text-slate-600 leading-relaxed font-light">
                        <p>{{ $displayBio }}</p>
                     </div>
                 </div>
                 
                 <div class="pt-2">
                     <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/Sigran_signature.png" class="h-12 w-auto mx-auto opacity-20" alt="Assinatura">
                 </div>
                </div>
            </div>
        </section>

        {{-- Categories --}}
        <section class="py-14 md:py-16">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="text-2xl font-bold text-slate-900 border-l-4 border-primary pl-4">Nossas Categorias</h2>
                    <a href="#" class="text-sm font-bold text-primary hover:text-slate-900 transition-colors flex items-center gap-1">
                        Ver todas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @foreach ($categories->take(6) as $cat)
                        <a href="{{ route('storefront.index', ['tenant' => $tenantSlug, 'category' => $cat->name]) }}"
                            class="group flex flex-col items-center gap-3 text-center">
                            <div class="w-full max-w-[9.5rem]">
                                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm group-hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1">
                                    <div class="absolute inset-0 bg-gradient-to-b from-black/0 via-black/0 to-black/10 pointer-events-none"></div>
                                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" style="background: radial-gradient(600px circle at 50% 30%, color-mix(in srgb, var(--primary-color) 22%, transparent), transparent 55%);"></div>
                                    <div class="aspect-square bg-slate-50">
                                        @if($cat->image_url)
                                            <img src="{{ $cat->image_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300 group-hover:text-primary transition-colors">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="font-bold text-slate-800 group-hover:text-primary transition-colors">{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Products --}}
        <section id="produtos" class="py-16 md:py-20">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Lançamentos da Semana</h2>
                    <p class="text-slate-500">Fique por dentro das últimas tendências e novidades que acabaram de chegar.</p>
                </div>
                
                {{-- Filter Tabs --}}
                <div class="flex justify-center mb-10 flex-wrap gap-2">
                     <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" 
                       class="px-5 py-2.5 rounded-full text-sm font-bold transition-all border {{ !request('category') ? 'bg-primary text-white shadow-lg border-primary' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300' }}">
                       Todos
                    </a>
                    @foreach($categories->take(5) as $cat)
                         <a href="{{ route('storefront.index', ['tenant' => $tenantSlug, 'category' => $cat->name]) }}" 
                           class="px-5 py-2.5 rounded-full text-sm font-bold transition-all border {{ request('category') == $cat->name ? 'bg-primary text-white shadow-lg border-primary' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300' }}">
                           {{ $cat->name }}
                        </a>
                    @endforeach
                </div>

                @if ($products->isEmpty())
                    <div class="bg-white rounded-3xl p-16 text-center border border-dashed border-slate-200">
                         <div class="mx-auto h-20 w-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Ops, nenhum produto aqui!</h3>
                        <p class="text-slate-500">Tente mudar os filtros ou volte mais tarde.</p>
                    <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" class="mt-6 inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary transition-colors">Limpar Filtros</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-6">
                        @foreach ($products as $product)
                            @include('storefront.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    
                    <div class="mt-16 text-center">
                        <button class="px-8 py-3 bg-white border border-slate-200 text-slate-900 font-bold rounded-full hover:bg-slate-50 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">
                            Carregar Mais Produtos
                        </button>
                    </div>
                @endif
            </div>
        </section>

        {{-- Newsletter --}}
        <section class="py-20 md:py-24 bg-primary-strong text-white relative overflow-hidden">
            {{-- Decoration --}}
            <div class="absolute top-0 right-0 p-12 opacity-10">
                <svg width="404" height="404" fill="none" viewBox="0 0 404 404" aria-hidden="true"><defs><pattern id="85737c0e-0916-41d7-917f-596dc7edfa27" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" fill="currentColor"></rect></pattern></defs><rect width="404" height="404" fill="url(#85737c0e-0916-41d7-917f-596dc7edfa27)"></rect></svg>
            </div>
            <div class="absolute bottom-0 left-0 p-12 opacity-10 transform rotate-180">
                 <svg width="404" height="404" fill="none" viewBox="0 0 404 404" aria-hidden="true"><defs><pattern id="85737c0e-0916-41d7-917f-596dc7edfa27" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" fill="currentColor"></rect></pattern></defs><rect width="404" height="404" fill="url(#85737c0e-0916-41d7-917f-596dc7edfa27)"></rect></svg>
            </div>

            <div class="relative max-w-4xl mx-auto px-4 text-center z-10">
                <h2 class="text-3xl md:text-5xl font-bold mb-6 tracking-tight">Faça parte do nosso clube</h2>
                <p class="text-slate-400 mb-10 text-lg max-w-2xl mx-auto leading-relaxed">
                    Inscreva-se para receber ofertas exclusivas, lançamentos antecipados e dicas de estilo diretamente no seu e-mail.
                </p>
                
                <form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
                    <input type="email" placeholder="Digite seu melhor e-mail"
                        class="flex-1 bg-white/10 border border-white/20 rounded-full px-6 py-4 text-white placeholder:text-slate-400 focus:outline-none focus:bg-white/15 focus:border-white/40 transition-all backdrop-blur-sm">
                    <button class="px-8 py-4 bg-white text-slate-900 font-bold rounded-full hover:bg-primary-soft transition-all shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-[0.99]">
                        Quero Participar
                    </button>
                </form>
                <p class="text-slate-600 text-xs mt-8">Prometemos não enviar spam. Seus dados estão seguros.</p>
            </div>
        </section>

    </main>

    <x-storefront.footer :tenant-slug="$tenantSlug" :store-settings="$storeSettings" />
</x-layouts.app>
