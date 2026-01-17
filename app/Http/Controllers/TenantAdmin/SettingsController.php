<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\StoreSettings;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = StoreSettings::current();

        $palettes = [
            ['name' => 'Preto (Padrão)', 'color' => '#0f172a'],
            ['name' => 'Azul', 'color' => '#2563eb'],
            ['name' => 'Verde', 'color' => '#059669'],
            ['name' => 'Roxo', 'color' => '#7c3aed'],
            ['name' => 'Rosa', 'color' => '#e11d48'],
            ['name' => 'Laranja', 'color' => '#d97706'],
        ];

        return view('tenant_admin.settings.edit', compact('settings', 'palettes'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'biography' => ['nullable', 'string', 'max:1000'],
            'banner_1' => ['nullable', 'image', 'max:5120'],
            'banner_2' => ['nullable', 'image', 'max:5120'],
            'banner_3' => ['nullable', 'image', 'max:5120'],
            'remove_banner_1' => ['nullable', 'boolean'],
            'remove_banner_2' => ['nullable', 'boolean'],
            'remove_banner_3' => ['nullable', 'boolean'],
        ]);

        $settings = StoreSettings::current();

        // Handle Images
        $imageFields = [
            'logo' => ['db_column' => 'logo_url', 'folder' => 'brand'],
            'banner_1' => ['db_column' => 'banner_1_url', 'folder' => 'banners'],
            'banner_2' => ['db_column' => 'banner_2_url', 'folder' => 'banners'],
            'banner_3' => ['db_column' => 'banner_3_url', 'folder' => 'banners'],
        ];

        foreach ($imageFields as $inputName => $config) {
            $removeInput = 'remove_' . $inputName;
            $dbColumn = $config['db_column'];

            // Handle removal
            if ($request->boolean($removeInput)) {
                $this->deleteLocalImageIfApplicable($settings->{$dbColumn});
                $validated[$dbColumn] = null;
            }

            // Handle upload
            if ($request->hasFile($inputName)) {
                $this->deleteLocalImageIfApplicable($settings->{$dbColumn});
                $validated[$dbColumn] = $this->storeTenantFile($request, $inputName, $config['folder']);
            }
        }

        // Clean up input array
        $inputKeys = array_keys($validated);
        foreach ($inputKeys as $key) {
            // Unset file inputs and remove flags
            if (in_array($key, array_keys($imageFields)) || str_starts_with($key, 'remove_')) {
                 unset($validated[$key]);
            }
        }

        $settings->update($validated);

        $tenant = app(Tenant::class);

        return redirect()
            ->route('tenant_admin.settings.edit', ['tenant' => $tenant->slug])
            ->with('status', 'Configurações atualizadas com sucesso!');
    }

    private function storeTenantFile(Request $request, string $inputName, string $folder): string
    {
        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;
        $tenantSlug = $tenant?->slug ?? 'default';

        $filePath = $request->file($inputName)->storePublicly(
            'tenants/'.$tenantSlug.'/'.$folder,
            'public'
        );

        return Storage::disk('public')->url($filePath);
    }

    private function deleteLocalImageIfApplicable(?string $imageUrl): void
    {
        if (! is_string($imageUrl) || $imageUrl === '') {
            return;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return;
        }

        $prefix = '/storage/';
        if (! str_starts_with($path, $prefix)) {
            return;
        }

        $relative = ltrim(substr($path, strlen($prefix)), '/');
        if ($relative === '') {
            return;
        }

        Storage::disk('public')->delete($relative);
    }
}
