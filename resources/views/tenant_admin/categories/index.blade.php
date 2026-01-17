<x-layouts.app :title="'Admin da Loja'" :subtitle="'Gerenciar Categorias'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)
    
    <style>
        /* Toggle Switch */
        .toggle-switch { position: relative; display: inline-block; width: 48px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; inset: 0; background-color: #cbd5e1; transition: 0.3s; border-radius: 24px; }
        .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%; }
        input:checked + .toggle-slider { background-color: #3b82f6; }
        input:checked + .toggle-slider:before { transform: translateX(24px); }
        input:disabled + .toggle-slider { opacity: 0.5; cursor: not-allowed; }
        
        /* Badge */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .badge-default { background: #dbeafe; color: #1e40af; }
        .badge-custom { background: #fef3c7; color: #92400e; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
    </style>
    
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Categorias</h1>
                <p class="text-slate-600">Ative ou desative categorias conforme necessário.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50">
                    Voltar para Produtos
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
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-slate-50 {{ !$category->is_active ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-200">
                                    @if ($category->image_url)
                                        <img src="{{ $category->image_url }}" class="h-full w-full object-cover" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900">{{ $category->name }}</span>
                                    @if ($category->is_default)
                                        <span class="badge badge-default">Padrão</span>
                                    @else
                                        <span class="badge badge-custom">Personalizada</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('tenant_admin.categories.toggle', ['categoryId' => $category->id, 'tenant' => $tenantSlug]) }}" method="POST" class="inline-block toggle-form">
                                    @csrf
                                    @method('PATCH')
                                    <label class="toggle-switch">
                                        <input type="checkbox" 
                                               @checked($category->is_active) 
                                               onchange="toggleCategory(this, '{{ $category->name }}', {{ $category->products()->where('is_active', true)->count() }})"
                                               data-form="true">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-3">
                                    @if (!$category->is_default)
                                        <a href="{{ route('tenant_admin.categories.edit', ['categoryId' => $category->id, 'tenant' => $tenantSlug]) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Editar</a>
                                        <form action="{{ route('tenant_admin.categories.destroy', ['categoryId' => $category->id, 'tenant' => $tenantSlug]) }}" method="POST" onsubmit="return confirm('Tem certeza? Esta ação não pode ser desfeita.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Excluir</button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs">Categoria padrão</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Nenhuma categoria cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-sm text-blue-900">
                    <p class="font-semibold mb-1">Sobre as categorias:</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-800">
                        <li><strong>Categorias Padrão</strong>: Criadas automaticamente para lojas de roupas e calçados. Não podem ser editadas ou excluídas.</li>
                        <li><strong>Status</strong>: Use o toggle para ativar/desativar categorias. Categorias com produtos ativos não podem ser desativadas.</li>
                        <li><strong>Categorias Personalizadas</strong>: Você pode criar suas próprias categorias conforme necessário.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<script>
function toggleCategory(checkbox, categoryName, activeProductCount) {
    const form = checkbox.closest('form');
    
    // Se está tentando desativar
    if (!checkbox.checked && activeProductCount > 0) {
        checkbox.checked = true; // Reverter
        alert(`Não é possível desativar "${categoryName}" porque existem ${activeProductCount} produto(s) ativo(s) nesta categoria.\n\nDesative os produtos primeiro.`);
        return;
    }
    
    // Confirmação ao desativar
    if (!checkbox.checked) {
        if (!confirm(`Desativar a categoria "${categoryName}"?\n\nEla não aparecerá mais nas opções de criação de produtos.`)) {
            checkbox.checked = true; // Reverter
            return;
        }
    }
    
    // Submeter form
    form.submit();
}
</script>

</x-layouts.app>
