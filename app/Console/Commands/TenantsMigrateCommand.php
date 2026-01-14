<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantsMigrateCommand extends Command
{
    protected $signature = 'tenants:migrate
        {--tenant= : Slug específico}
        {--force : Forçar execução mesmo em produção}';

    protected $description = 'Roda as migrations do tenant para um ou todos os tenants.';

    public function handle(): int
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
            $this->runTenantMigrations($tenant->schema);
        }

        return self::SUCCESS;
    }

    private function runTenantMigrations(string $schema): void
    {
        DB::purge('pgsql');
        DB::reconnect('pgsql');
        DB::connection('pgsql')->statement(sprintf('SET search_path TO "%s", public', $schema));

        Artisan::call('migrate', [
            '--database' => 'pgsql',
            '--path' => 'database/migrations/tenant',
            '--force' => (bool) $this->option('force'),
        ], $this->getOutput());
    }
}

