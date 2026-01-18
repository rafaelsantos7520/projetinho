@php($final = $product->promo_price_cents ?? $product->price_cents)
@php($from = $product->price_cents)
@php($discount = $from > 0 && $product->promo_price_cents ? max(0, (int) round((1 - ($final / $from)) * 100)) : 0)
@php($rating = (float) ($product->rating_avg ?? 0))
@php($count = (int) ($product->rating_count ?? 0))

<div class="group rounded-3xl border border-slate-200 bg-white overflow-hidden hover:shadow-2xl hover:shadow-slate-900/10 transition-all duration-300 hover:-translate-y-1">
    <a href="{{ route('storefront.product', ['tenant' => app(\App\Models\Tenant::class)->slug, 'product' => $product]) }}" class="block relative aspect-square bg-slate-100 overflow-hidden">
        <img
            src="{{ $product->primary_image_url ?? asset('images/product-placeholder.png') }}"
            onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.png') }}'"
            alt="Imagem do produto {{ $product->name }}"
            loading="lazy"
            decoding="async"
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
        />
        @if ($discount > 0)
            <span class="absolute top-3 left-3 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm" style="background-color: var(--primary-color)">
                -{{ $discount }}%
            </span>
        @endif
        
        {{-- Badges de Tamanho e Cor --}}
        @if ($product->size || $product->color)
            <div class="absolute bottom-3 left-3 right-3 flex flex-wrap gap-1.5">
                @if ($product->size)
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/90 text-slate-700 shadow-sm backdrop-blur-sm">
                        TAM: {{ $product->size }}
                    </span>
                @endif
                
                @if ($product->color)
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/90 text-slate-700 shadow-sm backdrop-blur-sm flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full border border-slate-300 bg-slate-400"></span>
                        {{ $product->color }}
                    </span>
                @endif
            </div>
        @endif
    </a>
    <div class="p-5">
        <a href="{{ route('storefront.product', ['tenant' => app(\App\Models\Tenant::class)->slug, 'product' => $product]) }}" class="block">
            <h3 class="font-bold text-slate-900 truncate transition-colors hover:opacity-80">{{ $product->name }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ $product->category->name ?? 'Geral' }}</p>
        </a>
        
        <div class="mt-4 flex items-end justify-between gap-2">
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-lg font-bold text-slate-900">R$ {{ number_format($final / 100, 2, ',', '.') }}</span>
                    @if ($product->promo_price_cents && $final !== $from)
                        <span class="text-xs text-slate-400 line-through">R$ {{ number_format($from / 100, 2, ',', '.') }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('storefront.product', ['tenant' => app(\App\Models\Tenant::class)->slug, 'product' => $product]) }}" class="h-10 w-10 rounded-full bg-slate-100 text-slate-900 flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm group-hover:shadow-md" aria-label="Ver produto">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
