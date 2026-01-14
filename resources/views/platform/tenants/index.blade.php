<x-layouts.app :title="'Plataforma - Lojas'" :subtitle="'Admin da plataforma'">
    <div class="rounded-2xl bg-white border border-slate-200 p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-lg font-semibold">Lojas (tenants)</div>
                <div class="text-sm text-slate-600">Lista de lojas cadastradas no schema público.</div>
            </div>
            <a href="{{ route('platform.tenants.create') }}"
                class="text-sm px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800">Criar loja</a>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-[720px] w-full text-sm">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium">Slug</th>
                        <th class="text-left px-4 py-3 font-medium">Schema</th>
                        <th class="text-left px-4 py-3 font-medium">Domain</th>
                        <th class="text-left px-4 py-3 font-medium">Links (dev)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($tenants as $tenant)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $tenant->slug }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $tenant->schema }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $tenant->domain ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a class="text-xs px-2 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-50"
                                        href="{{ route('storefront.index', ['tenant' => $tenant->slug]) }}">
                                        Catálogo
                                    </a>
                                    <a class="text-xs px-2 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-50"
                                        href="{{ route('tenant_admin.redirect', ['tenant' => $tenant->slug]) }}">
                                        Admin Produtos
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-600">Nenhuma loja cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
