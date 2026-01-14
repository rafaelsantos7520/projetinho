<x-layouts.app :title="'Admin da Loja'" :subtitle="'Editar Categoria'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h1 class="text-xl font-bold text-slate-900 mb-6">Editar Categoria</h1>

            <form action="{{ route('tenant_admin.categories.update', ['category' => $category->id, 'tenant' => $tenantSlug]) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nome da Categoria</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                    <a href="{{ route('tenant_admin.categories.index', ['tenant' => $tenantSlug]) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-colors">
                        Atualizar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>