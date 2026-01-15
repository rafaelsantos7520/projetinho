<x-layouts.app :title="'Admin da Loja'" :subtitle="'Editar Categoria'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Editar Categoria</h1>
                <p class="text-sm text-slate-500">Atualize nome e imagem da categoria.</p>
            </div>
            <a href="{{ route('tenant_admin.categories.index', ['tenant' => $tenantSlug]) }}" class="text-sm px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50">Voltar</a>
        </div>

        <form action="{{ route('tenant_admin.categories.update', ['category' => $category->id, 'tenant' => $tenantSlug]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5">
                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                            <div class="p-5 border-b border-slate-100">
                                <div class="font-bold text-slate-900">Imagem</div>
                                <div class="text-xs text-slate-500 mt-1">Substitua ou remova a imagem da categoria.</div>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
                                    <div class="relative h-36 sm:h-36" data-cat-box>
                                        <img src="{{ $category->image_url ?? '' }}" class="{{ $category->image_url ? '' : 'hidden' }} h-full w-full object-cover" data-cat-preview />
                                        <div class="{{ $category->image_url ? 'hidden' : '' }} h-full w-full flex items-center justify-center text-slate-500 text-sm" data-cat-placeholder>Clique para enviar</div>
                                        <div class="{{ $category->image_url ? 'flex' : 'hidden' }} absolute inset-0 bg-black/30 items-center justify-center gap-2" data-cat-overlay>
                                            <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-white shadow" data-cat-replace>Substituir</button>
                                            <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-white shadow text-red-600" data-cat-remove>Remover</button>
                                        </div>
                                    </div>
                                    <input type="file" name="image" accept="image/*" class="sr-only" data-cat-input>
                                    <input type="checkbox" name="remove_image" value="1" class="hidden" data-cat-remove-input>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-6">
                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                            <div class="p-5 border-b border-slate-100">
                                <div class="font-bold text-slate-900">Informações</div>
                                <div class="text-xs text-slate-500 mt-1">Nome e slug são atualizados automaticamente.</div>
                            </div>
                            <div class="p-5 space-y-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nome da Categoria</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('tenant_admin.categories.index', ['tenant' => $tenantSlug]) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancelar</a>
                            <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-colors">Atualizar Categoria</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>

    <script>
        (function () {
            const box = document.querySelector('[data-cat-box]');
            const input = document.querySelector('[data-cat-input]');
            const preview = document.querySelector('[data-cat-preview]');
            const placeholder = document.querySelector('[data-cat-placeholder]');
            const overlay = document.querySelector('[data-cat-overlay]');
            const replaceBtn = document.querySelector('[data-cat-replace]');
            const removeBtn = document.querySelector('[data-cat-remove]');
            const removeInput = document.querySelector('[data-cat-remove-input]');

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
                preview.src = URL.createObjectURL(file);
                removeInput.checked = false;
                setState(true);
            });

            setState(preview.getAttribute('src') ? true : false);
        })();
    </script>
</x-layouts.app>
