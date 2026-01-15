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
    @php($isTenantAdminArea = request()->is('admin*') && $hasTenant && !request()->routeIs('tenant_admin.login*'))

    @if ($isTenantAdminArea)
        <div class="min-h-screen bg-slate-50">
            <x-tenant-admin.header :tenant-query="$tenantQuery" :tenant-slug="$tenantSlug" :store-settings="$storeSettings ?? null" />
            
            <main class="mx-auto max-w-7xl px-4 pt-28 sm:pt-32 pb-10 sm:px-6 lg:px-8">
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
