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
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --primary-color: {{ $storeSettings->primary_color ?? '#0f172a' }};
            --primary-strong: color-mix(in srgb, var(--primary-color) 76%, #000);
            --primary-soft: color-mix(in srgb, var(--primary-color) 10%, #fff);
            --primary-soft-2: color-mix(in srgb, var(--primary-color) 16%, #fff);
            --primary-border: color-mix(in srgb, var(--primary-color) 26%, #fff);
        }
        .text-primary {
            color: var(--primary-color);
        }
        .bg-primary {
            background-color: var(--primary-color);
        }
        .bg-primary-strong {
            background-color: var(--primary-strong);
        }
        .bg-primary-soft {
            background-color: var(--primary-soft);
        }
        .border-primary {
            border-color: var(--primary-color);
        }
        .border-primary-soft {
            border-color: var(--primary-border);
        }
        .ring-primary {
            --tw-ring-color: var(--primary-color);
        }
        .hover\:bg-primary:hover {
            background-color: var(--primary-color);
        }
        .hover\:bg-primary-soft:hover {
            background-color: var(--primary-soft-2);
        }
        .hover\:text-primary:hover {
            color: var(--primary-color);
        }
        .hover\:border-primary:hover {
            border-color: var(--primary-color);
        }
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translate3d(0, 18px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        @keyframes slow-zoom {
            from {
                transform: scale(1.02);
            }
            to {
                transform: scale(1.08);
            }
        }
        @keyframes float {
            0%,
            100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(0, -6px, 0);
            }
        }
        .animate-fade-in-up {
            animation: fade-in-up 700ms cubic-bezier(.2, .8, .2, 1) both;
        }
        .animate-slow-zoom {
            animation: slow-zoom 10s ease-in-out both;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in-up,
            .animate-slow-zoom,
            .animate-float {
                animation: none !important;
            }
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
    @php($isTenantAdminArea = request()->is('admin*') && $hasTenant && !request()->routeIs('tenant_admin.login*'))

    @if ($isTenantAdminArea)
        <div class="min-h-screen bg-slate-50">
            <x-tenant-admin.header :tenant-query="$tenantQuery" :tenant-slug="$tenantSlug" :store-settings="$storeSettings ?? null" />
            
            {{-- Spacer para compensar o header fixo --}}
            <div class="h-20"></div>
            
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <x-ui.alerts />
                {{ $slot }}
            </main>
        </div>
    @else
        <div class="{{ $fullWidth ? 'w-full' : 'max-w-5xl mx-auto px-4 py-6 sm:py-10' }}">
            <x-storefront.header 
                :full-width="$fullWidth"
                :should-show-header="$shouldShowHeader"
                :store-settings="$storeSettings ?? null"
                :tenant-slug="$tenantSlug"
                :has-tenant="$hasTenant"
                :subtitle="$subtitle"
                :tenant-query="$tenantQuery"
                :is-platform-area="$isPlatformArea"
            />

            <x-ui.alerts />
            {{ $slot }}
        </div>
    @endif

    <script>
        (function() {
            // Modal Logic
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

            // Drawer Logic
            function toggleDrawer(open) {
                const drawer = document.querySelector('[data-admin-drawer]');
                if (!drawer) return;
                
                if (open) {
                    drawer.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    drawer.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }

            document.addEventListener('click', (e) => {
                // Modal triggers
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
                    return;
                }
                
                // Drawer triggers
                const openDrawerBtn = e.target.closest('[data-admin-drawer-open]');
                if (openDrawerBtn) {
                    e.preventDefault();
                    toggleDrawer(true);
                    return;
                }

                const closeDrawerBtn = e.target.closest('[data-admin-drawer-close]');
                if (closeDrawerBtn) {
                    e.preventDefault();
                    toggleDrawer(false);
                    return;
                }
            });
        })();
    </script>
</body>
</html>
