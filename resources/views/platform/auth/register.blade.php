<x-layouts.app :title="'Plataforma - Cadastro'" :subtitle="'Criar novo admin'" :show-header="false">
    <div class="max-w-md mx-auto">
        <div class="rounded-3xl bg-white border border-slate-200 p-6">
            <div class="text-lg font-semibold">Criar Administrador</div>
            <div class="text-sm text-slate-600 mt-1">Cadastre um novo acesso para gerenciar a plataforma.</div>

            <form method="POST" action="{{ route('platform.register.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Nome</label>
                    <input name="name" value="{{ old('name') }}" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input name="email" value="{{ old('email') }}" type="email" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Senha</label>
                    <input name="password" type="password" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                    @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confirmar Senha</label>
                    <input name="password_confirmation" type="password" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20" />
                </div>

                <button type="submit" class="w-full rounded-xl bg-slate-900 text-white px-4 py-2 font-medium hover:bg-slate-800 transition">
                    Criar e Entrar
                </button>
                
                <div class="text-center mt-4">
                    <a href="{{ route('platform.login') }}" class="text-sm text-slate-600 hover:text-slate-900 underline">
                        Já tenho conta
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
