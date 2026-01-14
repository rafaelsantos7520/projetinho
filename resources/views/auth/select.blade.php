<x-layouts.app :title="'Login'" :subtitle="'Escolha o tipo de acesso'">
    <div class="max-w-2xl mx-auto">
        <div class="rounded-2xl bg-white border border-slate-200 p-6">
            <div class="text-lg font-semibold">Escolha o login</div>
            <div class="text-sm text-slate-600 mt-1">Plataforma (criar/listar lojas) ou Admin da loja (produtos).</div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('platform.login') }}"
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5 hover:bg-slate-100">
                    <div class="font-semibold">Plataforma</div>
                    <div class="text-sm text-slate-600 mt-1">Criar e listar lojas (tenants).</div>
                </a>
                <a href="{{ route('tenant_admin.login', $tenantParam ?? []) }}"
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5 hover:bg-slate-100">
                    <div class="font-semibold">Admin da Loja</div>
                    <div class="text-sm text-slate-600 mt-1">Gerenciar produtos do tenant atual.</div>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
