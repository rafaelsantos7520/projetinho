<x-layouts.app :title="'Admin da Loja'" :subtitle="'Configurações da Loja'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Configurações</h1>
                <p class="text-sm text-slate-500">Personalize a aparência e informações da sua loja.</p>
            </div>
            <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="text-sm px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50">Voltar</a>
        </div>

        <form action="{{ route('tenant_admin.settings.update', ['tenant' => $tenantSlug]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- LEF SIDE -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- BRAND -->
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Marca</div>
                            <div class="text-xs text-slate-500 mt-1">Logo e cor principal da loja.</div>
                        </div>
                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- LOGO UPLOADER -->
                            <div data-image-uploader>
                                <div class="text-sm font-medium text-slate-700 mb-2">Logo da Loja</div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden relative group">
                                    <div class="relative h-32 cursor-pointer flex items-center justify-center bg-slate-50" data-image-box>
                                        <img src="{{ $settings->logo_url ?? '' }}" class="{{ $settings->logo_url ? '' : 'hidden' }} h-full w-full object-contain p-6" data-image-preview>
                                        <div class="{{ $settings->logo_url ? 'hidden' : '' }} flex flex-col items-center justify-center text-slate-400 gap-2" data-image-placeholder>
                                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-xs font-medium">Clique para enviar logo</span>
                                        </div>
                                        
                                        <!-- OVERLAY ACTIONS -->
                                        <div class="{{ $settings->logo_url ? 'flex' : 'hidden' }} absolute inset-0 bg-black/40 items-center justify-center gap-2 transition-opacity opacity-0 group-hover:opacity-100" data-image-overlay>
                                            <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-slate-900 shadow hover:bg-slate-100" data-image-replace>Alterar</button>
                                            <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-red-600 shadow hover:bg-red-50" data-image-remove>Remover</button>
                                        </div>
                                    </div>
                                    
                                    <input type="file" name="logo" accept="image/*" class="sr-only" data-image-input>
                                    <input type="checkbox" name="remove_logo" value="1" class="hidden" data-image-remove-input>
                                </div>
                            </div>

                            <!-- COLOR PICKER -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-3">Cor Principal</label>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($palettes as $palette)
                                        <label class="cursor-pointer group relative">
                                            <input type="radio" name="primary_color" value="{{ $palette['color'] }}" class="peer sr-only" 
                                                {{ old('primary_color', $settings->primary_color) === $palette['color'] ? 'checked' : '' }}>
                                            <div class="w-10 h-10 rounded-full shadow-sm border-2 border-transparent peer-checked:border-slate-900 peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-slate-900 transition-all flex items-center justify-center transform hover:scale-110" style="background-color: {{ $palette['color'] }}">
                                                <svg class="w-5 h-5 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTACT -->
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Contato & Redes</div>
                            <div class="text-xs text-slate-500 mt-1">Onde seus clientes te encontram.</div>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">WhatsApp</label>
                                <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-slate-900/20 bg-white">
                                    <div class="bg-slate-50 px-3 py-2.5 border-r border-slate-200 text-slate-500">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.506-.669-.516l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    </div>
                                    <input name="whatsapp_number" type="tel" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="(00) 00000-0000" class="w-full border-none focus:ring-0 text-sm px-3 py-2 bg-transparent">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Instagram</label>
                                <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-slate-900/20 bg-white">
                                    <div class="bg-slate-50 px-3 py-2.5 border-r border-slate-200 text-slate-500">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.765-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </div>
                                    <input name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="Link do perfil" class="w-full border-none focus:ring-0 text-sm px-3 py-2 bg-transparent">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Facebook</label>
                                <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-slate-900/20 bg-white">
                                    <div class="bg-slate-50 px-3 py-2.5 border-r border-slate-200 text-slate-500">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                    </div>
                                    <input name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="Link da página" class="w-full border-none focus:ring-0 text-sm px-3 py-2 bg-transparent">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE (Storefront) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                            <div>
                                <div class="font-bold text-slate-900">Loja Virtual</div>
                                <div class="text-xs text-slate-500 mt-1">Texto de apresentação e banners rotativos.</div>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-700 rounded-md">Novo</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <!-- BIO -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Sobre a Loja (Biografia)</label>
                                <textarea name="biography" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/20" placeholder="Conte um pouco sobre sua história...">{{ old('biography', $settings->biography) }}</textarea>
                                <div class="mt-1 text-xs text-slate-400">Aparecerá na página inicial da loja.</div>
                            </div>

                            <!-- BANNERS -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-4">Banners Promocionais</label>
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Banner 1 -->
                                    <div data-image-uploader class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Banner 1</div>
                                        <div class="rounded-xl border-2 border-dashed border-slate-300 bg-white overflow-hidden relative group hover:border-slate-400 transition-colors">
                                            <div class="relative w-full h-24 bg-slate-100 cursor-pointer flex items-center justify-center" data-image-box>
                                                <img src="{{ $settings->banner_1_url ?? '' }}" class="{{ $settings->banner_1_url ? '' : 'hidden' }} w-full h-full object-cover" data-image-preview>
                                                <div class="{{ $settings->banner_1_url ? 'hidden' : '' }} flex flex-col items-center justify-center text-slate-400 p-4 text-center" data-image-placeholder>
                                                    <svg class="w-8 h-8 opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span class="text-xs font-medium">Adicionar Banner</span>
                                                </div>
                                                <div class="{{ $settings->banner_1_url ? 'flex' : 'hidden' }} absolute inset-0 bg-black/40 items-center justify-center gap-2 transition-opacity opacity-0 group-hover:opacity-100" data-image-overlay>
                                                    <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-slate-900 shadow hover:bg-slate-100" data-image-replace>Trocar</button>
                                                    <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-red-600 shadow hover:bg-red-50" data-image-remove>Remover</button>
                                                </div>
                                            </div>
                                            <input type="file" name="banner_1" accept="image/*" class="sr-only" data-image-input>
                                            <input type="checkbox" name="remove_banner_1" value="1" class="hidden" data-image-remove-input>
                                        </div>
                                    </div>

                                    <!-- Banner 2 -->
                                    <div data-image-uploader class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Banner 2</div>
                                        <div class="rounded-xl border-2 border-dashed border-slate-300 bg-white overflow-hidden relative group hover:border-slate-400 transition-colors">
                                            <div class="relative w-full h-24 bg-slate-100 cursor-pointer flex items-center justify-center" data-image-box>
                                                <img src="{{ $settings->banner_2_url ?? '' }}" class="{{ $settings->banner_2_url ? '' : 'hidden' }} w-full h-full object-cover" data-image-preview>
                                                <div class="{{ $settings->banner_2_url ? 'hidden' : '' }} flex flex-col items-center justify-center text-slate-400 p-4 text-center" data-image-placeholder>
                                                    <svg class="w-8 h-8 opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span class="text-xs font-medium">Adicionar Banner</span>
                                                </div>
                                                <div class="{{ $settings->banner_2_url ? 'flex' : 'hidden' }} absolute inset-0 bg-black/40 items-center justify-center gap-2 transition-opacity opacity-0 group-hover:opacity-100" data-image-overlay>
                                                    <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-slate-900 shadow hover:bg-slate-100" data-image-replace>Trocar</button>
                                                    <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-red-600 shadow hover:bg-red-50" data-image-remove>Remover</button>
                                                </div>
                                            </div>
                                            <input type="file" name="banner_2" accept="image/*" class="sr-only" data-image-input>
                                            <input type="checkbox" name="remove_banner_2" value="1" class="hidden" data-image-remove-input>
                                        </div>
                                    </div>

                                    <!-- Banner 3 -->
                                    <div data-image-uploader class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Banner 3</div>
                                        <div class="rounded-xl border-2 border-dashed border-slate-300 bg-white overflow-hidden relative group hover:border-slate-400 transition-colors">
                                            <div class="relative w-full h-24 bg-slate-100 cursor-pointer flex items-center justify-center" data-image-box>
                                                <img src="{{ $settings->banner_3_url ?? '' }}" class="{{ $settings->banner_3_url ? '' : 'hidden' }} w-full h-full object-cover" data-image-preview>
                                                <div class="{{ $settings->banner_3_url ? 'hidden' : '' }} flex flex-col items-center justify-center text-slate-400 p-4 text-center" data-image-placeholder>
                                                    <svg class="w-8 h-8 opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span class="text-xs font-medium">Adicionar Banner</span>
                                                </div>
                                                <div class="{{ $settings->banner_3_url ? 'flex' : 'hidden' }} absolute inset-0 bg-black/40 items-center justify-center gap-2 transition-opacity opacity-0 group-hover:opacity-100" data-image-overlay>
                                                    <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-slate-900 shadow hover:bg-slate-100" data-image-replace>Trocar</button>
                                                    <button type="button" class="px-3 py-1.5 text-xs font-bold rounded-full bg-white text-red-600 shadow hover:bg-red-50" data-image-remove>Remover</button>
                                                </div>
                                            </div>
                                            <input type="file" name="banner_3" accept="image/*" class="sr-only" data-image-input>
                                            <input type="checkbox" name="remove_banner_3" value="1" class="hidden" data-image-remove-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                            Salvar Configurações
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Generic Image Uploader Logic
        document.querySelectorAll('[data-image-uploader]').forEach(uploader => {
            const box = uploader.querySelector('[data-image-box]');
            const input = uploader.querySelector('[data-image-input]');
            const preview = uploader.querySelector('[data-image-preview]');
            const placeholder = uploader.querySelector('[data-image-placeholder]');
            const overlay = uploader.querySelector('[data-image-overlay]');
            const replaceBtn = uploader.querySelector('[data-image-replace]');
            const removeBtn = uploader.querySelector('[data-image-remove]');
            const removeInput = uploader.querySelector('[data-image-remove-input]');

            if (!box || !input || !preview) return;

            const setState = (hasImage) => {
                if (hasImage) {
                    preview.classList.remove('hidden');
                    if(placeholder) placeholder.classList.add('hidden');
                    if(overlay) {
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                    }
                } else {
                    preview.classList.add('hidden');
                    if(placeholder) placeholder.classList.remove('hidden');
                    if(overlay) {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                    }
                }
            };

            box.addEventListener('click', () => input.click());
            
            if (replaceBtn) {
                replaceBtn.addEventListener('click', (e) => { 
                    e.stopPropagation(); 
                    input.click(); 
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if(confirm('Remover esta imagem?')) {
                        removeInput.checked = true;
                        input.value = '';
                        preview.removeAttribute('src');
                        setState(false);
                    }
                });
            }

            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                if (!file) return;
                
                removeInput.checked = false;
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    setState(true);
                }
                reader.readAsDataURL(file);
            });
            
            // Initial State check
            setState(!!preview.getAttribute('src'));
        });
    </script>
</x-layouts.app>
