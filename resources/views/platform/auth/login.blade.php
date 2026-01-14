<x-layouts.app :title="'Plataforma - Login'" :subtitle="'Admin da plataforma'" :show-header="false" >
    <div class="max-w-md mx-auto">
        <div class="rounded-3xl bg-white border border-slate-200 p-6">
            <div class="text-lg font-semibold">Entrar</div>
            <div class="text-sm text-slate-600 mt-1">Acesso do admin da plataforma.</div>

            <form method="POST" action="{{ route('platform.login.store') }}" class="mt-6 space-y-4">
                @csrf

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
        </div>
    </div>
</x-layouts.app>
