@props([
    'title' => null,
    'subtitle' => null,
    'showHeader' => true,
    'fullWidth' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary-color: {{ $storeSettings->primary_color ?? '#0f172a' }};
        }
    </style>
</head>

<body class="min-h-screen bg-white text-slate-900 font-sans antialiased">
    @php($tenantSlug = app()->bound(\App\Models\Tenant::class) ? app(\App\Models\Tenant::class)->slug : null)
    @php($tenantQuery = $tenantSlug ? ['tenant' => $tenantSlug] : [])
    @php($isAuthLikePage = request()->routeIs('tenant_admin.login*', 'platform.login*', 'login', 'onboarding.*'))
    @php($shouldShowHeader = (bool) $showHeader && !$isAuthLikePage)
    @php($isPlatformArea = request()->is('platform*'))
    @php($hasTenant = $tenantSlug !== null)

    <div class="{{ $fullWidth ? 'w-full' : 'max-w-5xl mx-auto px-4 py-6 sm:py-10' }}">
        @if ($shouldShowHeader)
            <header
                class="{{ $fullWidth ? 'max-w-7xl mx-auto px-4 py-6' : 'flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8' }}">
                @if ($fullWidth)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                @endif

                <div class="flex items-center gap-3">
                    @if (isset($storeSettings) && $storeSettings->logo_url)
                        <img src="{{ $storeSettings->logo_url }}" alt="Logo" class="h-12 w-auto object-contain">
                    @else
                        <div class="h-10 w-10 rounded-xl text-white flex items-center justify-center font-bold text-lg"
                            style="background-color: var(--primary-color)">
                            {{ substr($tenantSlug ? ucfirst($tenantSlug) : config('app.name'), 0, 2) }}
                        </div>
                    @endif
                    @if (!$hasTenant || (!isset($storeSettings->logo_url) || !$storeSettings->logo_url))
                        <div>
                            <div class="font-bold leading-tight">
                                {{ $tenantSlug ? ucfirst($tenantSlug) : config('app.name') }}</div>
                            <div class="text-sm text-slate-500">{{ $subtitle ?? 'Loja Virtual' }}</div>
                        </div>
                    @endif
                </div>

                <nav class="flex flex-wrap items-center gap-2">
                    @if (!request()->is('platform*') && $hasTenant)
                        <a href="{{ route('storefront.index', $tenantQuery) }}"
                            class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                            Loja
                        </a>
                    @endif

                    @if ($hasTenant)
                        @if (\Illuminate\Support\Facades\Auth::check())
                            <a href="{{ route('tenant_admin.products.index', $tenantQuery) }}"
                                class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                                Admin da Loja
                            </a>
                            <form method="POST" action="{{ route('tenant_admin.logout', $tenantQuery) }}">
                                @csrf
                                <input type="hidden" name="tenant" value="{{ $tenantSlug }}" />
                                <button
                                    class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                                    Sair (Loja)
                                </button>
                            </form>
                        @else
                            <a href="{{ route('tenant_admin.login', $tenantQuery) }}"
                                class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                                Entrar na Loja
                            </a>
                        @endif
                    @endif

                    @if ($isPlatformArea)
                        @if (\Illuminate\Support\Facades\Auth::guard('platform')->check())
                            <a href="{{ route('platform.tenants.index') }}"
                                class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                                Plataforma
                            </a>
                            <form method="POST" action="{{ route('platform.logout') }}">
                                @csrf
                                <button
                                    class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                                    Sair (Plat.)
                                </button>
                            </form>
                        @else
                            <a href="{{ route('platform.login') }}"
                                class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50">
                                Entrar (Plat.)
                            </a>
                            @if ($isPlatformArea || !$hasTenant)
                                <a href="{{ route('onboarding.create') }}"
                                    class="text-sm px-4 py-2 rounded-2xl bg-slate-900 text-white hover:bg-slate-800">
                                    Criar loja
                                </a>
                            @endif
                        @endif
                    @endif
                </nav>
                @if ($fullWidth)
    </div>
    @endif
    </header>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <div class="font-semibold mb-2">Corrija os erros abaixo</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{ $slot }}
    </div>

    <script>
        (function() {
            function openModal(id) {
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.remove('hidden');
                el.setAttribute('aria-hidden', 'false');
                const focusable = el.querySelector(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable) focusable.focus();
            }

            function closeModal(id) {
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.add('hidden');
                el.setAttribute('aria-hidden', 'true');
            }

            document.addEventListener('click', (e) => {
                const openBtn = e.target.closest('[data-open-modal]');
                if (openBtn) {
                    e.preventDefault();
                    openModal(openBtn.getAttribute('data-open-modal'));
                    return;
                }

                const closeBtn = e.target.closest('[data-close-modal]');
                if (closeBtn) {
                    e.preventDefault();
                    closeModal(closeBtn.getAttribute('data-close-modal'));
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                const opened = document.querySelectorAll('[id].fixed.inset-0.z-50:not(.hidden)');
                const last = opened[opened.length - 1];
                if (last && last.id) closeModal(last.id);
            });
        })();
    </script>
</body>

</html>
