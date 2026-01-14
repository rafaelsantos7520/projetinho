<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantsCreateCommand extends Command
{
    protected $signature = 'tenants:create
        {slug : Identificador da loja (ex.: loja-abc)}
        {--schema= : Nome do schema (default: tenant_<slug>)}
        {--domain= : Domínio completo (ex.: loja.exemplo.com)}
        {--no-migrate : Não rodar migrations do tenant}';

    protected $description = 'Cria um tenant (loja) e provisiona um schema no Postgres.';

    public function handle(): int
    {
        $slug = (string) $this->argument('slug');
        $schema = (string) ($this->option('schema') ?: 'tenant_'.Str::of($slug)->lower()->replace('-', '_'));
        $domain = $this->option('domain') !== null ? (string) $this->option('domain') : null;

        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\\-_]{2,60}$/', $slug)) {
            $this->error('Slug inválido. Use letras/números e hífen/underscore (mín. 3 chars).');
            return self::FAILURE;
        }

        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $schema)) {
            $this->error('Schema inválido. Use apenas letras/números/underscore e não comece com número.');
            return self::FAILURE;
        }

        $this->createSchema($schema);

        $tenant = Tenant::query()->create([
            'id' => (string) Str::uuid(),
            'slug' => $slug,
            'schema' => $schema,
            'domain' => $domain,
        ]);

        if (! $this->option('no-migrate')) {
            $this->runTenantMigrations($schema);
        }

        $this->info(sprintf('Tenant criado: %s (%s)', $tenant->slug, $tenant->schema));

        return self::SUCCESS;
    }

    private function createSchema(string $schema): void
    {
        DB::connection('landlord')->statement(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $schema));
    }

    private function runTenantMigrations(string $schema): void
    {
        DB::purge('pgsql');
        DB::reconnect('pgsql');
        DB::connection('pgsql')->statement(sprintf('SET search_path TO "%s", public', $schema));

        Artisan::call('migrate', [
            '--database' => 'pgsql',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ], $this->getOutput());
    }
}

