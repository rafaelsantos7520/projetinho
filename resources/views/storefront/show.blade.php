<x-layouts.app :title="$product->name" :subtitle="$product->category?->name ?? 'Loja'" :show-header="false" :full-width="true">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)
    @php($ratingAvg = (float) ($product->rating_avg ?? 0))
    @php($ratingCount = (int) ($product->rating_count ?? 0))
    @php($final = $product->promo_price_cents ?? $product->price_cents)
    @php($from = $product->compare_at_price_cents ?? $product->price_cents)
    @php($discount = $from > 0 ? max(0, (int) round((1 - $final / $from) * 100)) : 0)
    @php($gallery = $product->images->take(4))

    {{-- Header Simplificado (Reuso da Home recomendado, mas aqui simplificado para foco) --}}
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between gap-8">
            <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}" class="shrink-0 flex items-center gap-2">
                @if (isset($storeSettings) && $storeSettings->logo_url)
                    <img src="{{ $storeSettings->logo_url }}" alt="Logo" class="h-10 w-auto object-contain">
                @else
                    <div class="h-10 w-10 text-white rounded-lg flex items-center justify-center font-bold text-xl"
                        style="background-color: var(--primary-color)">
                        {{ strtoupper(substr($tenantSlug, 0, 1)) }}
                    </div>
                @endif
                @if (!isset($storeSettings->logo_url) || !$storeSettings->logo_url)
                    <span class="text-xl font-bold tracking-tight text-slate-900">{{ ucfirst($tenantSlug) }}</span>
                @endif
            </a>

            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                @foreach ($categories->take(5) as $cat)
                    <a href="{{ route('storefront.index', ['tenant' => $tenantSlug, 'category' => $cat->name]) }}"
                        class="hover:text-primary">{{ $cat->name }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}"
                    class="p-2 text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </a>
                <a href="#" class="p-2 text-slate-400 hover:text-slate-600 relative">
                    <span
                        class="absolute top-1 right-0 h-4 w-4 bg-primary text-white text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-white">1</span>
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
    </header>

    <main class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            {{-- Breadcrumb --}}
            <nav class="flex mb-8 text-sm text-slate-500">
                <a href="{{ route('storefront.index', ['tenant' => $tenantSlug]) }}"
                    class="hover:text-primary">Início</a>
                <span class="mx-2">/</span>
                @if ($product->category)
                    <a href="{{ route('storefront.index', ['tenant' => $tenantSlug, 'category' => $product->category->name]) }}"
                        class="hover:text-primary">{{ $product->category->name }}</a>
                    <span class="mx-2">/</span>
                @endif
                <span class="text-slate-900 font-medium">{{ $product->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
                {{-- Galeria de Imagens --}}
                <div class="space-y-4">
                    <div class="aspect-square bg-slate-100 rounded-3xl overflow-hidden relative group">
                        <img src="{{ $product->primary_image_url ?? 'https://picsum.photos/seed/' . urlencode((string) $product->id) . '/800/800' }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                        @if ($discount > 0)
                            <span
                                class="absolute top-4 left-4 bg-primary text-white text-sm font-bold px-3 py-1.5 rounded-full">
                                -{{ $discount }}% OFF
                            </span>
                        @endif
                    </div>
                    <div class="grid grid-cols-4 gap-4">
                        @if ($gallery->isEmpty())
                            @for ($i = 0; $i < 4; $i++)
                                <div
                                    class="aspect-square bg-slate-50 rounded-xl overflow-hidden cursor-pointer border-2 {{ $i === 0 ? 'border-primary' : 'border-transparent hover:border-slate-200' }}">
                                    <img src="{{ $product->primary_image_url ?? 'https://picsum.photos/seed/' . urlencode((string) $product->id) . '/200/200' }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endfor
                        @else
                            @foreach ($gallery as $img)
                                <div class="aspect-square bg-slate-50 rounded-xl overflow-hidden border-2 border-transparent hover:border-slate-200">
                                    <img src="{{ $img->image_url }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Detalhes do Produto --}}
                <div class="flex flex-col">
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">{{ $product->name }}</h1>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center text-amber-400">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $ratingAvg >= $i ? 'fill-current' : 'text-slate-200 fill-current' }}"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.158c.969 0 1.371 1.24.588 1.81l-3.363 2.444a1 1 0 00-.364 1.118l1.286 3.955c.3.921-.755 1.688-1.538 1.118l-3.363-2.444a1 1 0 00-1.176 0l-3.363 2.444c-.783.57-1.838-.197-1.538-1.118l1.286-3.955a1 1 0 00-.364-1.118L2.08 9.382c-.783-.57-.38-1.81.588-1.81h4.158a1 1 0 00.95-.69l1.286-3.955z" />
                                </svg>
                            @endfor
                            <span
                                class="ml-2 text-sm text-slate-500 font-medium">{{ number_format($ratingAvg, 1) }}
                                ({{ $ratingCount }} avaliações)</span>
                        </div>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="text-sm text-primary font-medium">Disponível em estoque</span>
                    </div>

                    <div class="flex items-baseline gap-4 mb-8">
                        <span class="text-5xl font-bold text-slate-900">R$
                            {{ number_format($final / 100, 2, ',', '.') }}</span>
                        @if ($final < $from)
                            <span class="text-xl text-slate-400 line-through">R$
                                {{ number_format($from / 100, 2, ',', '.') }}</span>
                        @endif
                    </div>

                    <div class="prose prose-slate mb-8 text-slate-600 leading-relaxed">
                        <p>{{ $product->description }}</p>
                    </div>

                    <div class="space-y-6 border-t border-slate-100 pt-8 mt-auto">
                        <div class="flex flex-col gap-4">
                            @if (isset($storeSettings->whatsapp_number) && $storeSettings->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $storeSettings->whatsapp_number) }}?text={{ urlencode('Olá, tenho interesse no produto ' . $product->name) }}"
                                    target="_blank"
                                    class="w-full text-white text-lg font-bold py-4 rounded-full shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2 hover:opacity-90"
                                    style="background-color: var(--primary-color)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21" />
                                        <path
                                            d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1" />
                                    </svg>
                                    Comprar no WhatsApp
                                </a>
                            @else
                                <button
                                    class="w-full bg-primary hover:bg-primary text-white text-lg font-bold py-4 rounded-full shadow-lg transition-all active:scale-[0.98]"
                                    Adicionar ao Carrinho
                                </button>
                                <button
                                    class="w-full bg-white border border-primary text-primary hover:bg-primary hover:text-white font-bold py-4 rounded-full transition-colors">
                                    Comprar Agora
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center justify-center gap-6 text-sm text-slate-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Compra Segura
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Entrega Garantida
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Produtos Relacionados --}}
    @if ($related->isNotEmpty())
        <section class="py-16 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-2xl font-bold text-slate-900 mb-8">Você também pode gostar</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($related as $rel)
                        @include('storefront.partials.product-card', ['product' => $rel])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Footer (Reuso da Home recomendado, mas aqui simplificado para foco) --}}
    <x-storefront.footer :tenant-slug="$tenantSlug" :store-settings="$storeSettings ?? null" />
</x-layouts.app>
