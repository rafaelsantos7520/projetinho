<x-layouts.app :title="'Admin da Loja'" :subtitle="'Produtos'" >
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
            <div>
                <div class="text-2xl font-bold text-slate-900">Editar produto</div>
                <div class="text-sm text-slate-500">Atualize informações, imagens e preços.</div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="text-sm px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50">Voltar</a>
                <a href="{{ route('storefront.product', ['tenant' => $tenantSlug, 'product' => $product->id]) }}" class="text-sm px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50" target="_blank">Ver na loja</a>
            </div>
        </div>

        <form method="POST" action="{{ route('tenant_admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6" x-data="{ 
            price: formatCurrency('{{ $product->price_cents }}'), 
            promo: formatCurrency('{{ $product->promo_price_cents }}'), 
            compare: formatCurrency('{{ $product->compare_at_price_cents }}') 
        }">
                @csrf
                @method('PUT')
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                            <div class="p-5 border-b border-slate-100">
                                <div class="font-bold text-slate-900">Imagens do produto</div>
                                <div class="text-xs text-slate-500 mt-1">Até 3 imagens. Substitua ou remova.</div>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-wrap gap-4">
                                    {{-- Slot 0 --}}
                                    @php($primary = $product->images[0] ?? null)
                                    <div class="relative w-24 h-24 shrink-0 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-colors overflow-hidden group cursor-pointer" style="width: 112px; height: 112px;" data-image-slot="edit-0">
                                        @if ($primary)
                                            <img src="{{ $primary->image_url }}" class="absolute inset-0 w-full h-full object-cover" data-image-preview="edit-0" />
                                            <input type="checkbox" name="remove_images[]" value="{{ $primary->id }}" class="hidden" data-remove-input="edit-0" />
                                        @else
                                            <img class="hidden absolute inset-0 w-full h-full object-cover" data-image-preview="edit-0" />
                                        @endif

                                        <div data-image-placeholder="edit-0" class="{{ $primary ? 'hidden' : '' }} absolute inset-0 flex flex-col items-center justify-center text-center p-2">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>

                                        <div data-image-overlay="edit-0" class="{{ $primary ? 'flex' : 'hidden' }} absolute inset-x-1 bottom-1 z-10">
                                            <div class="flex w-full gap-1">
                                                <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-white/90 text-slate-700 hover:bg-white shadow-sm backdrop-blur-sm" data-replace="edit-0">
                                                    Alterar
                                                </button>
                                                <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-red-50 text-red-600 hover:bg-red-100 shadow-sm backdrop-blur-sm" data-remove="edit-0">
                                                    Excluir
                                                </button>
                                            </div>
                                        </div>

                                        @if ($primary)
                                            <label class="absolute bottom-1 left-1 z-10 cursor-pointer p-1 rounded-md bg-white/80 backdrop-blur-sm shadow-sm" onclick="event.stopPropagation()">
                                                <input type="radio" name="primary_image_id" value="{{ $primary->id }}" class="w-3.5 h-3.5 border-slate-300 text-slate-900 focus:ring-0" checked title="Imagem Principal" />
                                            </label>
                                            <input type="file" name="replace_images[{{ $primary->id }}]" accept="image/*" class="hidden" data-file-input="edit-0" />
                                        @else
                                            <input type="file" name="add_images[0]" accept="image/*" class="hidden" data-file-input="edit-0" />
                                        @endif
                                    </div>

                                    {{-- Slots 1 & 2 --}}
                                    @for ($slot = 1; $slot < 3; $slot++)
                                        @php($img = $product->images[$slot] ?? null)
                                        @php($isPrimary = $img && (int) $img->sort_order === 0)
                                        <div class="relative w-24 h-24 shrink-0 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-colors overflow-hidden group cursor-pointer" style="width: 112px; height: 112px;" data-image-slot="edit-{{ $slot }}">
                                            @if ($img)
                                                <img src="{{ $img->image_url }}" class="absolute inset-0 w-full h-full object-cover" data-image-preview="edit-{{ $slot }}" />
                                                <input type="checkbox" name="remove_images[]" value="{{ $img->id }}" class="hidden" data-remove-input="edit-{{ $slot }}" />
                                            @else
                                                <img class="hidden absolute inset-0 w-full h-full object-cover" data-image-preview="edit-{{ $slot }}" />
                                            @endif

                                            <div data-image-placeholder="edit-{{ $slot }}" class="{{ $img ? 'hidden' : '' }} absolute inset-0 flex flex-col items-center justify-center text-center p-2">
                                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>

                                            <div data-image-overlay="edit-{{ $slot }}" class="{{ $img ? 'flex' : 'hidden' }} absolute inset-x-1 bottom-1 z-10">
                                                <div class="flex w-full gap-1">
                                                    <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-white/90 text-slate-700 hover:bg-white shadow-sm backdrop-blur-sm" data-replace="edit-{{ $slot }}">
                                                        Alterar
                                                    </button>
                                                    <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-red-50 text-red-600 hover:bg-red-100 shadow-sm backdrop-blur-sm" data-remove="edit-{{ $slot }}">
                                                        Excluir
                                                    </button>
                                                </div>
                                            </div>

                                            @if ($img)
                                                <label class="absolute bottom-1 left-1 z-10 cursor-pointer p-1 rounded-md bg-white/80 backdrop-blur-sm shadow-sm" onclick="event.stopPropagation()">
                                                    <input type="radio" name="primary_image_id" value="{{ $img->id }}" class="w-3.5 h-3.5 border-slate-300 text-slate-900 focus:ring-0" {{ $isPrimary ? 'checked' : '' }} title="Definir como Principal" />
                                                </label>
                                                <input type="file" name="replace_images[{{ $img->id }}]" accept="image/*" class="hidden" data-file-input="edit-{{ $slot }}" />
                                            @else
                                                <input type="file" name="add_images[{{ $slot }}]" accept="image/*" class="hidden" data-file-input="edit-{{ $slot }}" />
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                            <div class="p-5 border-b border-slate-100">
                                <div class="font-bold text-slate-900">Ações</div>
                                <div class="text-xs text-slate-500 mt-1">Salvar ou excluir o produto.</div>
                            </div>
                            <div class="p-5 flex items-center gap-3">
                                <button type="submit" class="rounded-xl bg-slate-900 text-white px-4 py-2 font-medium hover:bg-slate-800">Salvar</button>
                                <a href="#delete" class="rounded-xl bg-white border border-red-200 text-red-700 px-4 py-2 font-medium hover:bg-red-50">Excluir</a>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-6">
                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-900">Informações gerais</div>
                                    <div class="text-xs text-slate-500 mt-1">Nome, categoria e destaque.</div>
                                </div>
                            </div>
                            <div class="p-5 space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
                                    <input name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Categoria</label>
                                        <select name="category_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                            <option value="">Selecione...</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <input id="is_featured" name="is_featured" value="1" type="checkbox" class="h-4 w-4 rounded border-slate-300" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} />
                                            Produto em destaque
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                            <div class="p-5 border-b border-slate-100">
                                <div class="font-bold text-slate-900">Preços</div>
                                <div class="text-xs text-slate-500 mt-1">Preço, promoção e preço "de".</div>
                            </div>
                            <div class="p-5 space-y-5">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Preço</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">R$</span>
                                            <input type="text" name="price_formatted" x-model="price" x-on:input="price = inputMoney($event.target.value)" x-on:blur="price = blurMoney($event.target.value)" class="w-full rounded-xl border border-slate-200 pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Preço Promocional <span class="text-xs text-slate-400">(opcional)</span></label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">R$</span>
                                            <input type="text" name="promo_price_formatted" x-model="promo" x-on:input="promo = inputMoney($event.target.value)" x-on:blur="promo = blurMoney($event.target.value)" class="w-full rounded-xl border border-slate-200 pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Preço "De"</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">R$</span>
                                            <input type="text" name="compare_at_price_formatted" x-model="compare" x-on:input="compare = inputMoney($event.target.value)" x-on:blur="compare = blurMoney($event.target.value)" class="w-full rounded-xl border border-slate-200 pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Descrição</label>
                                    <textarea name="description" rows="5" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20">{{ old('description', $product->description) }}</textarea>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
        </form>

        <div id="delete" class="mt-6 bg-white border border-red-200 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-red-100">
                <div class="font-bold text-red-700">Excluir produto</div>
                <div class="text-xs text-slate-500 mt-1">Essa ação não pode ser desfeita.</div>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('tenant_admin.products.destroy', $product) }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />
                    <button type="submit" class="rounded-xl bg-white border border-red-200 text-red-700 px-4 py-2 font-medium hover:bg-red-50" onclick="return confirm('Excluir este produto?');">Confirmar exclusão</button>
                </form>
            </div>
        </div>
            
            <script>
                function formatCurrency(cents) {
                    if (!cents && cents !== 0) return '';
                    let value = (parseInt(cents) / 100).toFixed(2);
                    return value.replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                }
                function inputMoney(value) {
                    return value.replace(/[^0-9,]/g, '');
                }
                function blurMoney(value) {
                    if (!value) return '';
                    let v = value.replace(/\./g, '').replace(',', '.');
                    if (isNaN(v)) return value;
                    return parseFloat(v).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                }
            </script>

        <script>
            (function () {
                function setState(key, hasImage) {
                    const preview = document.querySelector(`[data-image-preview="${key}"]`);
                    const placeholder = document.querySelector(`[data-image-placeholder="${key}"]`);
                    const overlay = document.querySelector(`[data-image-overlay="${key}"]`);
                    if (!preview || !placeholder || !overlay) return;
                    if (hasImage) {
                        placeholder.classList.add('hidden');
                        preview.classList.remove('hidden');
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                        return;
                    }
                    placeholder.classList.remove('hidden');
                    preview.classList.add('hidden');
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                }

                function wire(key) {
                    const box = document.querySelector(`[data-image-slot="${key}"]`);
                    const input = document.querySelector(`[data-file-input="${key}"]`);
                    const replaceBtn = document.querySelector(`[data-replace="${key}"]`);
                    const removeBtn = document.querySelector(`[data-remove="${key}"]`);
                    const removeInput = document.querySelector(`[data-remove-input="${key}"]`);
                    const preview = document.querySelector(`[data-image-preview="${key}"]`);
                    const placeholder = document.querySelector(`[data-image-placeholder="${key}"]`);

                    if (!box || !input || !preview || !placeholder) return;

                    box.addEventListener('click', () => input.click());
                    if (replaceBtn) replaceBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        input.click();
                    });

                    if (removeBtn) removeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (removeInput) {
                            removeInput.checked = !removeInput.checked;
                            box.classList.toggle('opacity-40', removeInput.checked);
                            box.classList.toggle('grayscale', removeInput.checked);
                        } else {
                            input.value = '';
                            preview.removeAttribute('src');
                            setState(key, false);
                        }
                    });

                    input.addEventListener('change', () => {
                        const file = input.files && input.files[0];
                        if (!file) return;
                        preview.src = URL.createObjectURL(file);
                        setState(key, true);
                        if (removeInput) {
                            removeInput.checked = false;
                            box.classList.remove('opacity-40', 'grayscale');
                        }
                    });

                    setState(key, preview.getAttribute('src') ? true : false);
                }

                ['edit-0', 'edit-1', 'edit-2'].forEach(wire);

                const addMore = document.querySelector('[data-add-more]');
                if (addMore) {
                    addMore.addEventListener('click', () => {
                        for (const key of ['edit-0', 'edit-1', 'edit-2']) {
                            const preview = document.querySelector(`[data-image-preview="${key}"]`);
                            const input = document.querySelector(`[data-file-input="${key}"]`);
                            if (!preview || !input) continue;
                            const hasSrc = !!preview.getAttribute('src');
                            if (!hasSrc) {
                                input.click();
                                return;
                            }
                        }
                    });
                }
            })();
        </script>
</x-layouts.app>
