<x-layouts.app :title="'Escolha uma loja'" :subtitle="'Nenhum tenant resolvido'" >
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
            <div class="text-lg font-semibold">Criar sua loja</div>
            <div class="text-sm text-slate-600 mt-1">
                Cadastre sua loja e já crie o usuário proprietário.
            </div>

            <div class="mt-6">
                <x-ui.button variant="primary" class="w-full" :type="'button'" onclick="window.location.href='{{ route('onboarding.create') }}'">
                    Começar agora
                </x-ui.button>
            </div>

            <div class="mt-6 text-xs text-slate-600">
                Em dev você pode acessar lojas por query string ou header.
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="text-lg font-semibold">Entrar em uma loja existente</div>
            <div class="text-sm text-slate-600 mt-1">Informe o slug para abrir catálogo ou admin.</div>

            <form class="mt-6 space-y-3" id="enter-tenant-form">
                <div>
                    <label class="block text-sm font-medium mb-1">Slug</label>
                    <input name="tenant" placeholder="loja-abc" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-ui.button type="submit" variant="secondary" class="w-full" data-go="store">
                        Abrir catálogo
                    </x-ui.button>
                    <x-ui.button type="submit" variant="secondary" class="w-full" data-go="admin">
                        Abrir admin
                    </x-ui.button>
                </div>
            </form>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm font-semibold mb-2">Dev (rápido)</div>
                    <div class="text-sm text-slate-700">Use query string:</div>
                    <div class="mt-2 font-mono text-xs bg-white border border-slate-200 rounded-lg px-3 py-2">
                        http://localhost:8000/loja?tenant=loja-abc
                    </div>
                    <div class="mt-2 font-mono text-xs bg-white border border-slate-200 rounded-lg px-3 py-2">
                        http://localhost:8000/admin/login?tenant=loja-abc
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm font-semibold mb-2">Produção (recomendado)</div>
                    <div class="text-sm text-slate-700">Use subdomínio:</div>
                    <div class="mt-2 font-mono text-xs bg-white border border-slate-200 rounded-lg px-3 py-2">
                        https://loja-abc.seudominio.com
                    </div>
                    <div class="mt-2 font-mono text-xs bg-white border border-slate-200 rounded-lg px-3 py-2">
                        https://admin.loja-abc.seudominio.com
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <script>
        (function () {
            const form = document.getElementById('enter-tenant-form');
            if (!form) return;
            let mode = 'store';
            form.querySelectorAll('[data-go]').forEach((btn) => {
                btn.addEventListener('click', () => mode = btn.getAttribute('data-go') || 'store');
            });
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const tenant = new FormData(form).get('tenant');
                const slug = (tenant || '').toString().trim();
                if (!slug) return;
                window.location.href = (mode === 'admin' ? '/admin/login' : '/loja') + '?tenant=' + encodeURIComponent(slug);
            });
        })();
    </script>
</x-layouts.app>
