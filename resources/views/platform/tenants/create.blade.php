<x-layouts.app :title="'Plataforma - Criar Loja'" :subtitle="'Admin da plataforma'">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border border-slate-200 p-6">
            <div class="text-lg font-semibold mb-1">Criar nova loja</div>
            <div class="text-sm text-slate-600 mb-6">Cria um registro em tenants, cria o schema e roda migrations do
                tenant.</div>

            <form method="POST" action="{{ route('platform.tenants.store') }}" class="space-y-4"
                id="platform-create-tenant-form">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Slug</label>
                    <input name="slug" value="{{ old('slug') }}" placeholder="loja-abc"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    <div class="text-xs text-slate-500 mt-1">Usado no subdomínio e no schema (tenant_loja_abc).</div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Domínio (opcional)</label>
                    <input name="domain" value="{{ old('domain') }}" placeholder="minhaloja.com"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    <div class="text-xs text-slate-500 mt-1">Se preencher, a loja também pode ser acessada por esse
                        host.</div>
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <div class="text-sm font-semibold">Proprietário da loja</div>
                    <div class="text-xs text-slate-600 mt-1">Já cria o usuário do admin da loja.</div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nome</label>
                    <input name="owner_name" value="{{ old('owner_name') }}" placeholder="Seu nome"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input name="owner_email" value="{{ old('owner_email') }}" type="email"
                        placeholder="voce@exemplo.com"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Senha</label>
                        <input name="password" type="password" minlength="8"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                            data-password />
                        <div class="mt-2 text-xs" data-password-hint></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirmar senha</label>
                        <input name="password_confirmation" type="password" minlength="8"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                            data-password-confirm />
                        <div class="mt-2 text-xs" data-password-confirm-hint></div>
                    </div>
                </div>

                <button id="platform-create-tenant-submit"
                    class="w-full rounded-xl bg-slate-900 text-white px-4 py-2 font-medium hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Criar loja
                </button>
            </form>

            <div class="mt-6">
                <a href="{{ route('platform.tenants.index') }}"
                    class="text-sm px-3 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 inline-flex">
                    Ver lojas cadastradas
                </a>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-6">
            <div class="text-lg font-semibold mb-1">Acesso rápido</div>
            <div class="text-sm text-slate-600 mb-6">Links para validar a loja recém-criada.</div>

            @if (session('created_tenant_slug'))
                @php($slug = session('created_tenant_slug'))
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 mb-4">
                    <div class="text-sm font-medium">Loja criada: <span class="font-mono">{{ $slug }}</span>
                    </div>
                    <div class="text-xs text-slate-600 mt-1">No dev você pode usar query string, e em produção use
                        subdomínio.</div>
                </div>

                <div class="space-y-2">
                    <a class="block rounded-xl border border-slate-200 bg-white px-4 py-3 hover:bg-slate-50"
                        href="{{ route('storefront.index', ['tenant' => $slug]) }}">
                        <div class="text-sm font-medium">Abrir catálogo (dev)</div>
                        <div class="text-xs text-slate-600">{{ route('storefront.index', ['tenant' => $slug]) }}</div>
                    </a>
                    <a class="block rounded-xl border border-slate-200 bg-white px-4 py-3 hover:bg-slate-50"
                        href="{{ route('tenant_admin.products.index', ['tenant' => $slug]) }}">
                        <div class="text-sm font-medium">Abrir admin de produtos (dev)</div>
                        <div class="text-xs text-slate-600">
                            {{ route('tenant_admin.products.index', ['tenant' => $slug]) }}</div>
                    </a>
                </div>
            @else
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    Crie uma loja para aparecerem links de teste aqui.
                </div>
            @endif
        </div>
    </div>

    <div id="loading-overlay" class="fixed inset-0 z-40 hidden">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="rounded-2xl bg-white border border-slate-200 px-5 py-4 text-sm font-medium">
                Criando loja...
            </div>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('platform-create-tenant-form');
            const submit = document.getElementById('platform-create-tenant-submit');
            const password = form ? form.querySelector('[data-password]') : null;
            const confirm = form ? form.querySelector('[data-password-confirm]') : null;
            const hint = form ? form.querySelector('[data-password-hint]') : null;
            const confirmHint = form ? form.querySelector('[data-password-confirm-hint]') : null;
            const overlay = document.getElementById('loading-overlay');

            function validate() {
                if (!password || !confirm || !submit) return;
                const email = form.querySelector('input[name="owner_email"]');
                const name = form.querySelector('input[name="owner_name"]');
                const slug = form.querySelector('input[name="slug"]');
                const p = password.value || '';
                const c = confirm.value || '';
                const okLen = p.length >= 8;
                const okMatch = p.length > 0 && p === c;
                const okEmail = email ? email.checkValidity() : false;
                const okName = name ? (name.value || '').trim().length > 0 : false;
                const okSlug = slug ? (slug.value || '').trim().length >= 3 : false;

                if (hint) {
                    hint.textContent = okLen ? 'Senha com tamanho OK.' : 'Mínimo 8 caracteres.';
                    hint.className = 'mt-2 text-xs ' + (okLen ? 'text-emerald-700' : 'text-slate-600');
                }
                if (confirmHint) {
                    confirmHint.textContent = okMatch ? 'Senhas conferem.' : 'As senhas precisam ser iguais.';
                    confirmHint.className = 'mt-2 text-xs ' + (okMatch ? 'text-emerald-700' : 'text-slate-600');
                }

                submit.disabled = !(okLen && okMatch && okEmail && okName && okSlug);
            }

            if (password) password.addEventListener('input', validate);
            if (confirm) confirm.addEventListener('input', validate);
            if (form) {
                form.querySelectorAll('input[name="owner_email"], input[name="owner_name"], input[name="slug"]')
                    .forEach((el) => {
                        el.addEventListener('input', validate);
                    });
            }
            validate();

            if (form) {
                form.addEventListener('submit', () => {
                    if (!overlay) return;
                    overlay.classList.remove('hidden');
                });
            }
        })();
    </script>
</x-layouts.app>
