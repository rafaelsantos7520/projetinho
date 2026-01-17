<?php

namespace App\Tenancy;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenancyProvisioner
{
    public function createTenant(
        string $slug,
        ?string $domain = null,
        ?string $schema = null,
        bool $runMigrations = true,
        ?string $ownerEmail = null,
        ?string $ownerPassword = null,
        ?string $ownerName = null,
    ): Tenant {
        $schema = $schema ?: 'tenant_' . Str::of($slug)->lower()->replace('-', '_');

        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\\-_]{2,60}$/', $slug)) {
            abort(422, 'Slug inválido.');
        }

        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $schema)) {
            abort(422, 'Schema inválido.');
        }

        $this->createTenantContainerIfNotExists($schema);

        $tenant = Tenant::query()->create([
            'id' => (string) Str::uuid(),
            'slug' => $slug,
            'schema' => $schema,
            'domain' => $domain,
        ]);

        if ($runMigrations) {
            $this->runTenantMigrations($schema);
            $this->createDefaultCategories($schema);
        }

        if ($ownerEmail !== null && $ownerPassword !== null) {
            $this->createOwnerUser($schema, $ownerEmail, $ownerPassword, $ownerName);
        }

        return $tenant;
    }

    public function runTenantMigrations(string $schema): void
    {
        $tenantConnection = (string) config('tenancy.tenant_connection', config('database.default'));

        DB::purge($tenantConnection);
        config(["database.connections.$tenantConnection.database" => $schema]);
        DB::reconnect($tenantConnection);

        $driver = DB::connection($tenantConnection)->getDriverName();
        if ($driver === 'pgsql') {
            DB::connection($tenantConnection)->statement(sprintf('SET search_path TO "%s", public', $schema));
        }

        Artisan::call('migrate', [
            '--database' => $tenantConnection,
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }

    private function createOwnerUser(string $schema, string $email, string $password, ?string $name = null): void
    {
        $tenantConnection = (string) config('tenancy.tenant_connection', config('database.default'));

        DB::purge($tenantConnection);
        config(["database.connections.$tenantConnection.database" => $schema]);
        DB::reconnect($tenantConnection);

        $driver = DB::connection($tenantConnection)->getDriverName();
        if ($driver === 'pgsql') {
            DB::connection($tenantConnection)->statement(sprintf('SET search_path TO "%s", public', $schema));
        }

        User::on($tenantConnection)->updateOrCreate(
            ['email' => $email],
            ['name' => $name ?: 'Proprietário', 'password' => $password],
        );

        if ($driver === 'pgsql') {
            DB::connection($tenantConnection)->statement('SET search_path TO public');
        }
    }

    private function createTenantContainerIfNotExists(string $schema): void
    {
        $landlordConnection = 'landlord';
        $driver = DB::connection($landlordConnection)->getDriverName();

        if ($driver === 'pgsql') {
            DB::connection($landlordConnection)->statement(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $schema));

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $escaped = str_replace('`', '``', $schema);
            try {
                DB::connection($landlordConnection)->statement(
                    'CREATE DATABASE IF NOT EXISTS `' . $escaped . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
                );
            } catch (\Throwable) {
                abort(422, 'Não foi possível criar o banco do tenant automaticamente. Crie o banco manualmente e tente novamente.');
            }

            return;
        }

        abort(500, 'Unsupported database driver for tenancy provisioning.');
    }

    private function createDefaultCategories(string $schema): void
    {
        $tenantConnection = (string) config('tenancy.tenant_connection', config('database.default'));

        DB::purge($tenantConnection);
        config(["database.connections.$tenantConnection.database" => $schema]);
        DB::reconnect($tenantConnection);

        $driver = DB::connection($tenantConnection)->getDriverName();
        if ($driver === 'pgsql') {
            DB::connection($tenantConnection)->statement(sprintf('SET search_path TO "%s", public', $schema));
        }

        // Categorias padrão para lojas de roupas e calçados
        $defaultCategories = [
            ['name' => 'Camisetas', 'slug' => 'camisetas', 'sort_order' => 1],
            ['name' => 'Camisas', 'slug' => 'camisas', 'sort_order' => 2],
            ['name' => 'Blusas', 'slug' => 'blusas', 'sort_order' => 3],
            ['name' => 'Regatas', 'slug' => 'regatas', 'sort_order' => 4],
            ['name' => 'Calças', 'slug' => 'calcas', 'sort_order' => 5],
            ['name' => 'Shorts', 'slug' => 'shorts', 'sort_order' => 6],
            ['name' => 'Bermudas', 'slug' => 'bermudas', 'sort_order' => 7],
            ['name' => 'Vestidos', 'slug' => 'vestidos', 'sort_order' => 8],
            ['name' => 'Saias', 'slug' => 'saias', 'sort_order' => 9],
            ['name' => 'Moletons', 'slug' => 'moletons', 'sort_order' => 10],
            ['name' => 'Jaquetas', 'slug' => 'jaquetas', 'sort_order' => 11],
            ['name' => 'Casacos', 'slug' => 'casacos', 'sort_order' => 12],
            ['name' => 'Conjuntos', 'slug' => 'conjuntos', 'sort_order' => 13],
            ['name' => 'Tênis', 'slug' => 'tenis', 'sort_order' => 14],
            ['name' => 'Sapatos', 'slug' => 'sapatos', 'sort_order' => 15],
            ['name' => 'Sandálias', 'slug' => 'sandalias', 'sort_order' => 16],
            ['name' => 'Chinelos', 'slug' => 'chinelos', 'sort_order' => 17],
            ['name' => 'Botas', 'slug' => 'botas', 'sort_order' => 18],
            ['name' => 'Acessórios', 'slug' => 'acessorios', 'sort_order' => 19],
            ['name' => 'Bolsas', 'slug' => 'bolsas', 'sort_order' => 20],
        ];

        foreach ($defaultCategories as $category) {
            DB::connection($tenantConnection)->table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => $category['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($driver === 'pgsql') {
            DB::connection($tenantConnection)->statement('SET search_path TO public');
        }
    }
}
