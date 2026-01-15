<x-layouts.app :title="'Admin da Loja'" :subtitle="'Gerenciar Categorias'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)
    
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Categorias</h1>
                <p class="text-slate-600">Gerencie as categorias dos seus produtos.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50">
                    Voltar para Produtos
                </a>
                <a href="{{ route('tenant_admin.categories.create', ['tenant' => $tenantSlug]) }}" class="px-4 py-2 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800">
                    Nova Categoria
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 font-medium text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Imagem</th>
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-200">
                                    @if ($category->image_url)
                                        <img src="{{ $category->image_url }}" class="h-full w-full object-cover" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('tenant_admin.categories.edit', ['category' => $category->id, 'tenant' => $tenantSlug]) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    <form action="{{ route('tenant_admin.categories.destroy', ['category' => $category->id, 'tenant' => $tenantSlug]) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                Nenhuma categoria cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
