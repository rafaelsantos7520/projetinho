<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Tenancy\TenantSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantsMigrateCommand extends Command
{
    protected $signature = 'tenants:migrate
        {--tenant= : Slug específico}
        {--force : Forçar execução mesmo em produção}';

    protected $description = 'Roda as migrations do tenant para um ou todos os tenants.';

    public function handle(TenantSchemaManager $schemaManager): int
    {
        $slug = $this->option('tenant') !== null ? (string) $this->option('tenant') : null;

        $query = Tenant::query()->orderBy('slug');
        if ($slug !== null) {
            $query->where('slug', $slug);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->warn('Nenhum tenant encontrado.');

            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->line(sprintf('Migrando tenant: %s (%s)', $tenant->slug, $tenant->schema));
            $this->runTenantMigrations($schemaManager, $tenant->schema);
        }

        return self::SUCCESS;
    }

    private function runTenantMigrations(TenantSchemaManager $schemaManager, string $schema): void
    {
        $schemaManager->setSearchPath($schema, true);
        $tenantConnection = (string) config('tenancy.tenant_connection', config('database.default'));
        Artisan::call('migrate', [
            '--database' => $tenantConnection,
            '--path' => 'database/migrations/tenant',
            '--force' => (bool) $this->option('force'),
        ], $this->getOutput());
    }
}
