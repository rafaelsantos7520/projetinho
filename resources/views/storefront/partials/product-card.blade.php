@php($final = $product->promo_price_cents ?? $product->price_cents)
@php($from = $product->compare_at_price_cents ?? $product->price_cents)
@php($discount = $from > 0 ? max(0, (int) round((1 - ($final / $from)) * 100)) : 0)
@php($rating = (float) ($product->rating_avg ?? 0))
@php($count = (int) ($product->rating_count ?? 0))

<div class="group rounded-3xl border border-slate-200 bg-white overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <a href="{{ route('storefront.product', ['tenant' => app(\App\Models\Tenant::class)->slug, 'product' => $product->id]) }}" class="block relative aspect-square bg-slate-100 overflow-hidden">
        <img
            src="{{ $product->primary_image_url ?? ('https://picsum.photos/seed/'.urlencode((string) $product->id).'/500/500') }}"
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
    </a>
    <div class="p-5">
        <a href="{{ route('storefront.product', ['tenant' => app(\App\Models\Tenant::class)->slug, 'product' => $product->id]) }}" class="block">
            <h3 class="font-bold text-slate-900 truncate transition-colors hover:opacity-80">{{ $product->name }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ $product->category->name ?? 'Geral' }}</p>
        </a>
        
        <div class="mt-4 flex items-end justify-between gap-2">
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-lg font-bold text-slate-900">R$ {{ number_format($final / 100, 2, ',', '.') }}</span>
                    @if ($final !== $from)
                        <span class="text-xs text-slate-400 line-through">R$ {{ number_format($from / 100, 2, ',', '.') }}</span>
                    @endif
                </div>
            </div>
            <button type="button" class="h-10 w-10 rounded-full bg-slate-100 text-slate-900 flex items-center justify-center hover:text-white transition-colors" style="--hover-bg: var(--primary-color)" onmouseover="this.style.backgroundColor=getComputedStyle(document.documentElement).getPropertyValue('--primary-color')" onmouseout="this.style.backgroundColor=''" aria-label="Adicionar ao carrinho">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>
