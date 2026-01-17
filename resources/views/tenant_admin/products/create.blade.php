<x-layouts.app :title="'Admin da Loja'" :subtitle="'Novo Produto'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    <div class="max-w-7xl mx-auto">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Novo produto</h1>
                <p class="text-sm text-slate-500">Cadastre o produto com imagens, preço e categoria.</p>
            </div>
            <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="text-sm px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50">Voltar</a>
        </div>

        <form action="{{ route('tenant_admin.products.store', ['tenant' => $tenantSlug]) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ price: '{{ old('price_formatted', '') }}', promo: '{{ old('promo_price_formatted', '') }}', compare: '{{ old('compare_at_price_formatted', '') }}' }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Informações gerais</div>
                            <div class="text-xs text-slate-500 mt-1">Nome, categoria e destaque.</div>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
                                <input name="name" value="{{ old('name') }}" placeholder="Nome do produto" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" required>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Categoria</label>
                                    <select name="category_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                        <option value="">Selecione...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('tenant_admin.categories.create', ['tenant' => $tenantSlug]) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-900 mt-2 inline-block">Nova Categoria</a>
                                </div>

                                <div class="flex items-end">
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300">
                                        Produto em destaque
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Preço e descrição</div>
                            <div class="text-xs text-slate-500 mt-1">Preço, desconto e descrição.</div>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Preço</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">R$</span>
                                        <input
                                            type="text"
                                            name="price_formatted"
                                            x-model="price"
                                            x-on:input="price = inputMoney($event.target.value)"
                                            x-on:blur="price = blurMoney($event.target.value)"
                                            required
                                            inputmode="decimal"
                                            autocomplete="off"
                                            placeholder="0,00"
                                            class="w-full rounded-xl border border-slate-200 pl-12 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                                        >
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Preço Promocional</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">R$</span>
                                        <input
                                            type="text"
                                            name="promo_price_formatted"
                                            x-model="promo"
                                            x-on:input="promo = inputMoney($event.target.value)"
                                            x-on:blur="promo = blurMoney($event.target.value)"
                                            inputmode="decimal"
                                            autocomplete="off"
                                            placeholder="0,00"
                                            class="w-full rounded-xl border border-slate-200 pl-12 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Descrição</label>
                                <textarea name="description" rows="5" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" placeholder="Descrição">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Imagens do produto</div>
                            <div class="text-xs text-slate-500 mt-1">Até 3 imagens. Escolha uma principal.</div>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex flex-wrap gap-4">
                                <div class="relative w-24 h-24 shrink-0 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-colors overflow-hidden cursor-pointer group" style="width: 112px; height: 112px;" data-image-box="create-0">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-2" data-image-placeholder="create-0">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <img data-image-preview="create-0" class="hidden absolute inset-0 w-full h-full object-cover" />
                                    <div data-image-actions="create-0" class="hidden absolute inset-x-1 bottom-1 z-10">
                                        <div class="flex w-full gap-1">
                                            <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-white/90 text-slate-700 hover:bg-white shadow-sm backdrop-blur-sm" data-replace-button="create-0">
                                                Alterar
                                            </button>
                                            <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-red-50 text-red-600 hover:bg-red-100 shadow-sm backdrop-blur-sm" data-remove-button="create-0">
                                                Excluir
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" name="images[0]" accept="image/*" class="hidden" data-image-input="create-0">
                                </div>

                                @for ($i = 1; $i < 3; $i++)
                                    <div class="relative w-24 h-24 shrink-0 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-colors overflow-hidden cursor-pointer group" style="width: 112px; height: 112px;" data-image-box="create-{{ $i }}">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-2" data-image-placeholder="create-{{ $i }}">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <img data-image-preview="create-{{ $i }}" class="hidden absolute inset-0 w-full h-full object-cover" />
                                        <div data-image-actions="create-{{ $i }}" class="hidden absolute inset-x-1 bottom-1 z-10">
                                            <div class="flex w-full gap-1">
                                                <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-white/90 text-slate-700 hover:bg-white shadow-sm backdrop-blur-sm" data-replace-button="create-{{ $i }}">
                                                    Alterar
                                                </button>
                                                <button type="button" class="flex-1 px-2 py-1 text-[10px] font-medium rounded-full bg-red-50 text-red-600 hover:bg-red-100 shadow-sm backdrop-blur-sm" data-remove-button="create-{{ $i }}">
                                                    Excluir
                                                </button>
                                            </div>
                                        </div>
                                        <input type="file" name="images[{{ $i }}]" accept="image/*" class="hidden" data-image-input="create-{{ $i }}">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <div class="font-bold text-slate-900">Ações</div>
                            <div class="text-xs text-slate-500 mt-1">Finalizar cadastro.</div>
                        </div>
                        <div class="p-5 flex flex-col gap-3">
                            <button type="submit" class="w-full rounded-xl bg-slate-900 text-white px-4 py-3 font-medium hover:bg-slate-800">Salvar produto</button>
                            <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="w-full text-center rounded-xl bg-white border border-slate-200 text-slate-600 px-4 py-3 font-medium hover:bg-slate-50">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
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
            function setSlotState(key, hasImage) {
                const img = document.querySelector(`[data-image-preview="${key}"]`);
                const placeholder = document.querySelector(`[data-image-placeholder="${key}"]`);
                const actions = document.querySelector(`[data-image-actions="${key}"]`);
                if (!img || !placeholder || !actions) return;
                if (hasImage) {
                    img.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    actions.classList.remove('hidden');
                    actions.classList.add('flex');
                    return;
                }
                img.classList.add('hidden');
                placeholder.classList.remove('hidden');
                actions.classList.add('hidden');
                actions.classList.remove('flex');
            }

            function wireSlot(key) {
                const input = document.querySelector(`[data-image-input="${key}"]`);
                const img = document.querySelector(`[data-image-preview="${key}"]`);
                const replaceBtn = document.querySelector(`[data-replace-button="${key}"]`);
                const removeBtn = document.querySelector(`[data-remove-button="${key}"]`);
                if (!input || !img) return;

                input.addEventListener('change', () => {
                    const file = input.files && input.files[0];
                    if (!file) return;
                    img.src = URL.createObjectURL(file);
                    setSlotState(key, true);
                });

                const box = document.querySelector(`[data-image-box="${key}"]`);
                if (box) box.addEventListener('click', () => input.click());

                if (replaceBtn) replaceBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    input.click();
                });
                if (removeBtn) removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    input.value = '';
                    img.removeAttribute('src');
                    setSlotState(key, false);
                });

                setSlotState(key, false);
            }

            const keys = ['create-0', 'create-1', 'create-2'];
            keys.forEach(wireSlot);

            const addBtn = document.querySelector('[data-add-image]');
            if (addBtn) {
                addBtn.addEventListener('click', () => {
                    for (const key of keys) {
                        const input = document.querySelector(`[data-image-input="${key}"]`);
                        if (input && (!input.files || input.files.length === 0)) {
                            input.click();
                            break;
                        }
                    }
                });
            }
        })();
    </script>
</x-layouts.app>
