<x-layouts.app :title="'Criar sua loja'" :subtitle="'Cadastro rápido'" :show-header="false" >
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
            <div class="text-lg font-semibold">Criar nova loja</div>
            <div class="text-sm text-slate-600 mt-1">Já cria o usuário proprietário e prepara a loja.</div>

            <form method="POST" action="{{ route('onboarding.store') }}" class="mt-6 space-y-4" id="store-signup-form">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Slug da loja</label>
                        <input name="slug" value="{{ old('slug') }}" placeholder="loja-abc" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                        <div class="text-xs text-slate-500 mt-1">Usado no subdomínio e no schema.</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Domínio (opcional)</label>
                        <input name="domain" value="{{ old('domain') }}" placeholder="minhaloja.com" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                        <div class="text-xs text-slate-500 mt-1">Pode ser configurado depois.</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nome do proprietário</label>
                    <input name="owner_name" value="{{ old('owner_name') }}" placeholder="Seu nome" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email do proprietário</label>
                    <input name="owner_email" value="{{ old('owner_email') }}" placeholder="voce@exemplo.com" type="email" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Senha</label>
                        <input name="password" type="password" minlength="8" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" data-password />
                        <div class="mt-2 text-xs" data-password-hint></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirmar senha</label>
                        <input name="password_confirmation" type="password" minlength="8" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" data-password-confirm />
                        <div class="mt-2 text-xs" data-password-confirm-hint></div>
                    </div>
                </div>

                <x-ui.button type="submit" class="w-full" id="store-signup-submit" disabled>
                    Criar loja
                </x-ui.button>

                <div class="text-xs text-slate-600">
                    Ao criar, você será redirecionado para o login do admin da loja.
                </div>
            </form>
        </x-ui.card>

        <x-ui.card>
            <div class="text-lg font-semibold">Já tem uma loja?</div>
            <div class="text-sm text-slate-600 mt-1">Entre no admin informando o slug.</div>

            <form class="mt-6 space-y-3" id="enter-store-form">
                <div>
                    <label class="block text-sm font-medium mb-1">Slug</label>
                    <input name="tenant" placeholder="loja-abc" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>
                <x-ui.button type="submit" variant="secondary" class="w-full">
                    Entrar no admin da loja
                </x-ui.button>
            </form>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                Ex.: <span class="font-mono">/admin/login?tenant=loja-abc</span>
            </div>
        </x-ui.card>
    </div>

    <div id="loading-overlay" class="fixed inset-0 z-40 hidden">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="rounded-2xl bg-white border border-slate-200 px-5 py-4 text-sm font-medium">
                Criando sua loja...
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('store-signup-form');
            const submit = document.getElementById('store-signup-submit');
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
                form.querySelectorAll('input[name="owner_email"], input[name="owner_name"], input[name="slug"]').forEach((el) => {
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

            const enter = document.getElementById('enter-store-form');
            if (enter) {
                enter.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const tenant = new FormData(enter).get('tenant');
                    const slug = (tenant || '').toString().trim();
                    if (!slug) return;
                    window.location.href = '/admin/login?tenant=' + encodeURIComponent(slug);
                });
            }
        })();
    </script>
</x-layouts.app>
