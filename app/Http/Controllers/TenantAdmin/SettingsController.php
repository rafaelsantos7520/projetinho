<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\StoreSettings;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'logo_url' => ['nullable', 'url', 'max:2048'],
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
        ]);

        $settings = StoreSettings::current();
        $settings->update($validated);

        $tenant = app(Tenant::class);
        return redirect()
            ->route('tenant_admin.settings.edit', ['tenant' => $tenant->slug])
            ->with('status', 'Configurações atualizadas com sucesso!');
    }
}