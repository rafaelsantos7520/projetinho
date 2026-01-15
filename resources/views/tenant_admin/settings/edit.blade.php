<x-layouts.app :title="'Admin da Loja'" :subtitle="'Configurações da Loja'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Configurações</h1>
                <p class="text-sm text-slate-500">Identidade visual e canais de contato.</p>
            </div>
            <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="text-sm px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50">Voltar</a>
        </div>

        <form action="{{ route('tenant_admin.settings.update', ['tenant' => $tenantSlug]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Brand</div>
                            <div class="text-xs text-slate-500 mt-1">Logo e cor principal.</div>
                        </div>
                        <div class="p-5 space-y-5">
                            <div>
                                <div class="text-sm font-medium text-slate-700 mb-2">Logo</div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
                                    <div class="relative h-40 sm:h-48" data-logo-box>
                                        <img src="{{ $settings->logo_url ?? '' }}" class="{{ $settings->logo_url ? '' : 'hidden' }} h-full w-full object-contain p-6" data-logo-preview>
                                        <div class="{{ $settings->logo_url ? 'hidden' : '' }} h-full w-full flex items-center justify-center text-slate-500 text-sm" data-logo-placeholder>Clique para enviar</div>
                                        <div class="{{ $settings->logo_url ? 'flex' : 'hidden' }} absolute inset-0 bg-black/30 items-center justify-center gap-2" data-logo-overlay>
                                            <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-white shadow" data-logo-replace>Substituir</button>
                                            <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-white shadow text-red-600" data-logo-remove>Remover</button>
                                        </div>
                                    </div>
                                    <input type="file" name="logo" accept="image/*" class="sr-only" data-logo-input>
                                    <input type="checkbox" name="remove_logo" value="1" class="hidden" data-logo-remove-input>
                                </div>
                                <div class="mt-3">
                                    <div class="text-xs text-slate-500">A logo usa upload (recomendado). A URL continua aceita no backend, mas foi removida do formulário.</div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-3">Cor Principal</label>
                                <div class="flex flex-wrap gap-4">
                                    @foreach($palettes as $palette)
                                        <label class="cursor-pointer group relative">
                                            <input type="radio" name="primary_color" value="{{ $palette['color'] }}" class="peer sr-only" 
                                                {{ old('primary_color', $settings->primary_color) === $palette['color'] ? 'checked' : '' }}>
                                            <div class="w-12 h-12 rounded-full shadow-sm border-2 border-transparent peer-checked:border-slate-900 peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-slate-900 transition-all flex items-center justify-center"
                                                style="background-color: {{ $palette['color'] }}">
                                                <svg class="w-6 h-6 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-medium text-slate-500 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ $palette['name'] }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Contato & Redes</div>
                            <div class="text-xs text-slate-500 mt-1">Links e WhatsApp da loja.</div>
                        </div>
                        <div class="p-5 space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">WhatsApp (apenas números)</label>
                                <input name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="5511999999999" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                <div class="mt-1 text-xs text-slate-500">Se preenchido, o botão "Comprar" envia o cliente para o WhatsApp.</div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Instagram (URL)</label>
                                    <input name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/..." class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Facebook (URL)</label>
                                    <input name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/..." class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-colors">Salvar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const box = document.querySelector('[data-logo-box]');
            const input = document.querySelector('[data-logo-input]');
            const preview = document.querySelector('[data-logo-preview]');
            const placeholder = document.querySelector('[data-logo-placeholder]');
            const overlay = document.querySelector('[data-logo-overlay]');
            const replaceBtn = document.querySelector('[data-logo-replace]');
            const removeBtn = document.querySelector('[data-logo-remove]');
            const removeInput = document.querySelector('[data-logo-remove-input]');

            if (!box || !input || !preview || !placeholder || !overlay || !removeInput) return;

            function setState(hasImage) {
                if (hasImage) {
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                    return;
                }
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }

            box.addEventListener('click', () => input.click());
            if (replaceBtn) replaceBtn.addEventListener('click', (e) => { e.stopPropagation(); input.click(); });
            if (removeBtn) removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removeInput.checked = true;
                input.value = '';
                preview.removeAttribute('src');
                setState(false);
            });

            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                if (!file) return;
                removeInput.checked = false;
                preview.src = URL.createObjectURL(file);
                setState(true);
            });

            setState(preview.getAttribute('src') ? true : false);
        })();
    </script>
</x-layouts.app>
