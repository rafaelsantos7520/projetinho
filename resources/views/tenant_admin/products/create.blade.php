<x-layouts.app :title="'Admin da Loja'" :subtitle="'Novo Produto'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-slate-900">Criar Produto</h1>
                <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancelar</a>
            </div>

            <form action="{{ route('tenant_admin.products.store', ['tenant' => $tenantSlug]) }}" method="POST" class="space-y-6" x-data="{ price: '', promo: '', compare: '' }">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
                    <input name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Imagem (URL)</label>
                    <input name="image_url" value="{{ old('image_url') }}" placeholder="https://..." class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Categoria</label>
                    <select name="category_id" class="w-full rounded-xl border-slate-200 bg-white focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Selecione...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-1 text-xs">
                        <a href="{{ route('tenant_admin.categories.create', ['tenant' => $tenantSlug]) }}" class="text-indigo-600 hover:underline">Nova Categoria</a>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input id="is_featured" name="is_featured" value="1" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900" {{ old('is_featured') ? 'checked' : '' }} />
                    <label for="is_featured" class="text-sm font-medium text-slate-700">Produto em destaque</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Preço</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-500">R$</span>
                        <input type="text" name="price_formatted" x-model="price" 
                            x-on:input="price = inputMoney($event.target.value)"
                            x-on:blur="price = blurMoney($event.target.value)"
                            required
                            class="w-full rounded-xl border-slate-200 pl-9 pr-3 focus:ring-slate-900 focus:border-slate-900" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Preço Promocional</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-500">R$</span>
                            <input type="text" name="promo_price_formatted" x-model="promo"
                                x-on:input="promo = inputMoney($event.target.value)"
                                x-on:blur="promo = blurMoney($event.target.value)"
                                class="w-full rounded-xl border-slate-200 pl-9 pr-3 focus:ring-slate-900 focus:border-slate-900" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Preço 'De'</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-500">R$</span>
                            <input type="text" name="compare_at_price_formatted" x-model="compare"
                                x-on:input="compare = inputMoney($event.target.value)"
                                x-on:blur="compare = blurMoney($event.target.value)"
                                class="w-full rounded-xl border-slate-200 pl-9 pr-3 focus:ring-slate-900 focus:border-slate-900" />
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Descrição</label>
                    <textarea name="description" rows="4" class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">{{ old('description') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-colors">
                        Salvar Produto
                    </button>
                </div>
            </form>
        </div>
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
</x-layouts.app>