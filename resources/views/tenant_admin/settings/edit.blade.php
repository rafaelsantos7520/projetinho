<x-layouts.app :title="'Admin da Loja'" :subtitle="'Configurações da Loja'">
    @php($tenantSlug = app(\App\Models\Tenant::class)->slug)

    <div class="max-w-2xl mx-auto">
        
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Identidade Visual</h1>
                    <p class="text-sm text-slate-500">Personalize a aparência da sua loja.</p>
                </div>
                <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Voltar</a>
            </div>

            <form action="{{ route('tenant_admin.settings.update', ['tenant' => $tenantSlug]) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                {{-- Logo URL --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Logo (URL)</label>
                    <div class="flex gap-4 items-start">
                        <div class="flex-1">
                            <input name="logo_url" value="{{ old('logo_url', $settings->logo_url) }}" placeholder="https://..." class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">
                            <p class="text-xs text-slate-500 mt-1">Cole a URL da sua logo (recomendado: PNG transparente).</p>
                        </div>
                        <div class="h-16 w-16 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden">
                            @if($settings->logo_url)
                                <img src="{{ $settings->logo_url }}" class="max-h-full max-w-full" />
                            @else
                                <span class="text-xs text-slate-400">Logo</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Cor Principal --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">Cor Principal</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach($palettes as $palette)
                            <label class="cursor-pointer group relative">
                                <input type="radio" name="primary_color" value="{{ $palette['color'] }}" class="peer sr-only" 
                                    {{ old('primary_color', $settings->primary_color) === $palette['color'] ? 'checked' : '' }}>
                                
                                <div class="w-12 h-12 rounded-full shadow-sm border-2 border-transparent peer-checked:border-slate-900 peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-slate-900 transition-all flex items-center justify-center"
                                     style="background-color: {{ $palette['color'] }}">
                                     <svg class="w-6 h-6 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                     </svg>
                                </div>
                                <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-medium text-slate-500 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ $palette['name'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-6">Escolha uma das paletas de alto contraste pré-selecionadas.</p>
                </div>

                <div class="border-t border-slate-100 my-6"></div>

                <div>
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Contato & Redes Sociais</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">WhatsApp (apenas números)</label>
                            <input name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="5511999999999" class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">
                            <p class="text-xs text-slate-500 mt-1">Se preenchido, o botão "Comprar" enviará o cliente para o WhatsApp.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Instagram (URL)</label>
                                <input name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/..." class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Facebook (URL)</label>
                                <input name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/..." class="w-full rounded-xl border-slate-200 focus:ring-slate-900 focus:border-slate-900">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-colors">
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>