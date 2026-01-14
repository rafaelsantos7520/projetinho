@props(['tenantSlug', 'storeSettings'])

<footer class="bg-white border-t border-slate-200 py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 flex flex-col items-center">
        {{-- Redes Sociais --}}
        @if((isset($storeSettings->instagram_url) && $storeSettings->instagram_url) || (isset($storeSettings->facebook_url) && $storeSettings->facebook_url))
            <div class="flex gap-6 mb-8">
                @if(isset($storeSettings->instagram_url) && $storeSettings->instagram_url)
                    <a href="{{ $storeSettings->instagram_url }}" target="_blank" class="text-slate-400 hover:text-pink-600 transition-colors transform hover:scale-110 duration-200" title="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                @endif
                @if(isset($storeSettings->facebook_url) && $storeSettings->facebook_url)
                    <a href="{{ $storeSettings->facebook_url }}" target="_blank" class="text-slate-400 hover:text-blue-600 transition-colors transform hover:scale-110 duration-200" title="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                @endif
            </div>
        @endif

        <p class="text-slate-500 text-sm mb-4">© {{ date('Y') }} {{ ucfirst($tenantSlug) }}. Todos os direitos reservados.</p>
        
        <a href="{{ route('tenant_admin.redirect', ['tenant' => $tenantSlug]) }}" class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            Área do Lojista
        </a>
    </div>
</footer>