<x-layouts.app :title="'Admin da Loja - Login'" :subtitle="'Acesso do admin da loja'" :show-header="false" >
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)
    <div class="max-w-md mx-auto">
        <div class="rounded-3xl bg-white border border-slate-200 p-6">
            <div class="text-lg font-semibold">Entrar</div>
            <div class="text-sm text-slate-600 mt-1">Acesso do admin da loja (tenant atual).</div>

            <form method="POST" action="{{ route('tenant_admin.login.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />

                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input name="email" value="{{ old('email') }}" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Senha</label>
                    <input name="password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>

                <button class="w-full rounded-xl bg-slate-900 text-white px-4 py-2 font-medium hover:bg-slate-800">
                    Entrar
                </button>
            </form>

            <div class="mt-4 flex items-center justify-between text-sm">
                <a href="#" data-open-modal="recover-password" class="text-slate-700 hover:underline">Esqueci minha senha</a>
            </div>
        </div>
    </div>

    <x-ui.modal id="recover-password" title="Recuperar senha" description="Esse fluxo é um modal de UI (sem envio de email).">
        <form class="space-y-4" data-fake-recover>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input name="email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" placeholder="seuemail@exemplo.com" />
            </div>
            <button class="w-full rounded-xl bg-slate-900 text-white px-4 py-2 font-medium hover:bg-slate-800">
                Enviar link
            </button>
            <div class="text-xs text-slate-600">
                Para produção: integrar com o reset de senha do Laravel (mail + tokens).
            </div>
        </form>
    </x-ui.modal>

    <script>
        (function () {
            const recoverForm = document.querySelector('[data-fake-recover]');
            if (!recoverForm) return;
            recoverForm.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Em produção, aqui enviaria o email de recuperação.');
            });
        })();
    </script>
</x-layouts.app>
