<x-layouts.app :title="'Admin da Loja'" :subtitle="'Editar produto'" >
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)
    <div class="max-w-2xl">
        <div class="rounded-3xl bg-white border border-slate-200 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-lg font-semibold">Editar produto</div>
                    <div class="text-sm text-slate-600">Atualize as informações do produto.</div>
                </div>
                <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">Voltar</a>
            </div>

            <form method="POST" action="{{ route('tenant_admin.products.update', $product) }}" class="mt-6 space-y-4" x-data="{ 
                price: formatCurrency('{{ $product->price_cents }}'), 
                promo: formatCurrency('{{ $product->promo_price_cents }}'), 
                compare: formatCurrency('{{ $product->compare_at_price_cents }}') 
            }">
                @csrf
                @method('PUT')
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />

                <div>
                    <label class="block text-sm font-medium mb-1">Nome</label>
                    <input name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Imagem (URL)</label>
                    <input name="image_url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://..." class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Categoria</label>
                    <select name="category_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                        <option value="">Selecione...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input id="is_featured" name="is_featured" value="1" type="checkbox" class="h-4 w-4 rounded border-slate-300" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} />
                    <label for="is_featured" class="text-sm">Produto em destaque</label>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Preço</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-500">R$</span>
                        <input type="text" name="price_formatted" x-model="price" 
                            x-on:input="price = inputMoney($event.target.value)"
                            x-on:blur="price = blurMoney($event.target.value)"
                            class="w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Preço Promocional</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-500">R$</span>
                            <input type="text" name="promo_price_formatted" x-model="promo"
                                x-on:input="promo = inputMoney($event.target.value)"
                                x-on:blur="promo = blurMoney($event.target.value)"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Preço 'De'</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-500">R$</span>
                            <input type="text" name="compare_at_price_formatted" x-model="compare"
                                x-on:input="compare = inputMoney($event.target.value)"
                                x-on:blur="compare = blurMoney($event.target.value)"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <textarea name="description" rows="5" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Avaliação média (0-5)</label>
                        <input name="rating_avg" value="{{ old('rating_avg', $product->rating_avg) }}" type="number" min="0" max="5" step="0.1" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Qtd. avaliações</label>
                        <input name="rating_count" value="{{ old('rating_count', $product->rating_count) }}" type="number" min="0" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="rounded-xl bg-slate-900 text-white px-4 py-2 font-medium hover:bg-slate-800">
                        Salvar alterações
                    </button>
                </div>
            </form>
            
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

            <form method="POST" action="{{ route('tenant_admin.products.destroy', $product) }}" class="mt-4">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />
                <button class="rounded-xl bg-white border border-red-200 text-red-700 px-4 py-2 font-medium hover:bg-red-50">
                    Excluir produto
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
