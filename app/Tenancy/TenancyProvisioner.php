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
        $schema = $schema ?: 'tenant_'.Str::of($slug)->lower()->replace('-', '_');

        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\\-_]{2,60}$/', $slug)) {
            abort(422, 'Slug inválido.');
        }

        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $schema)) {
            abort(422, 'Schema inválido.');
        }

        DB::connection('landlord')->statement(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $schema));

        $tenant = Tenant::query()->create([
            'id' => (string) Str::uuid(),
            'slug' => $slug,
            'schema' => $schema,
            'domain' => $domain,
        ]);

        if ($runMigrations) {
            $this->runTenantMigrations($schema);
        }

        if ($ownerEmail !== null && $ownerPassword !== null) {
            $this->createOwnerUser($schema, $ownerEmail, $ownerPassword, $ownerName);
        }

        return $tenant;
    }

    public function runTenantMigrations(string $schema): void
    {
        DB::purge('pgsql');
        DB::reconnect('pgsql');
        DB::connection('pgsql')->statement(sprintf('SET search_path TO "%s", public', $schema));

        Artisan::call('migrate', [
            '--database' => 'pgsql',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }

    private function createOwnerUser(string $schema, string $email, string $password, ?string $name = null): void
    {
        DB::purge('pgsql');
        DB::reconnect('pgsql');
        DB::connection('pgsql')->statement(sprintf('SET search_path TO "%s", public', $schema));

        User::on('pgsql')->updateOrCreate(
            ['email' => $email],
            ['name' => $name ?: 'Proprietário', 'password' => $password],
        );

        DB::connection('pgsql')->statement('SET search_path TO public');
    }
}
