<?php

namespace App\Console\Commands;

use App\Tenancy\TenancyProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TenantsCreateCommand extends Command
{
    protected $signature = 'tenants:create
        {slug : Identificador da loja (ex.: loja-abc)}
        {--schema= : Nome do schema/banco (default: tenant_<slug>)}
        {--domain= : Domínio completo (ex.: loja.exemplo.com)}
        {--no-migrate : Não rodar migrations do tenant}';

    protected $description = 'Cria um tenant (loja) e provisiona um schema/banco para o tenant.';

    public function handle(TenancyProvisioner $provisioner): int
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

        $tenant = $provisioner->createTenant(
            slug: $slug,
            domain: $domain,
            schema: $schema,
            runMigrations: ! (bool) $this->option('no-migrate'),
        );

        $this->info(sprintf('Tenant criado: %s (%s)', $tenant->slug, $tenant->schema));

        return self::SUCCESS;
    }
}
