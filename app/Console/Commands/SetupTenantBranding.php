<?php

namespace App\Console\Commands;

use App\Models\StoreSettings;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class SetupTenantBranding extends Command
{
    protected $signature = 'tenant:setup-branding {tenant : Slug do tenant}';
    protected $description = 'Configura logo e banners iniciais para um tenant';

    public function handle(): int
    {
        $tenantSlug = $this->argument('tenant');
        $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();

        if (!$tenant) {
            $this->error("Tenant '{$tenantSlug}' não encontrado!");
            return self::FAILURE;
        }

        $this->info("Configurando branding para: {$tenantSlug}...");

        // Configurar conexão do tenant
        config(['database.connections.tenant.database' => $tenant->schema]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');
        app()->instance(\App\Models\Tenant::class, $tenant);

        // Arquivo local para usar (o placeholder "emdigital")
        $sourcePath = public_path('images/product-placeholder.png');
        if (!file_exists($sourcePath)) {
            $this->error("Imagem local não encontrada em: {$sourcePath}");
            return self::FAILURE;
        }

        // Disco de destino (usando o mesmo disco de product images para consistência)
        $diskName = config('filesystems.product_images_disk', 'public');
        $disk = Storage::disk($diskName);

        // Upload Logo
        $logoPath = "tenants/{$tenantSlug}/branding/logo.png";
        $disk->putFileAs("tenants/{$tenantSlug}/branding", new File($sourcePath), 'logo.png', 'public');
        $logoUrl = $disk->url($logoPath);
        
        // Upload Banner 1 (Usando a mesma imagem por enquanto)
        $banner1Path = "tenants/{$tenantSlug}/branding/banner-1.png";
        $disk->putFileAs("tenants/{$tenantSlug}/branding", new File($sourcePath), 'banner-1.png', 'public');
        $banner1Url = $disk->url($banner1Path);

        // Atualizar configurações
        $settings = StoreSettings::current();
        $settings->update([
            'logo_url' => $logoUrl,
            'banner_1_url' => $banner1Url,
            'primary_color' => '#059669', // Verde Esmeralda (Exemplo do print)
            'biography' => 'Moda feminina elegante e sofisticada para todas as ocasiões.',
            'whatsapp_number' => '11999999999'
        ]);

        $this->info("✅ Branding configurado com sucesso!");
        $this->info("Logo URL: {$logoUrl}");

        return self::SUCCESS;
    }
}
