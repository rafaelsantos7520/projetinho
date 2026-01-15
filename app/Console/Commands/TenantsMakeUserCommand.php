<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TenantsMakeUserCommand extends Command
{
    protected $signature = 'tenants:make-user
        {tenant : Slug do tenant}
        {email : Email do usuário}
        {--name=Admin : Nome do usuário}
        {--password= : Senha (se omitir, vai pedir)}';

    protected $description = 'Cria/atualiza um usuário dentro do schema do tenant.';

    public function handle(TenantSchemaManager $schemaManager): int
    {
        $slug = (string) $this->argument('tenant');
        $email = (string) $this->argument('email');
        $name = (string) $this->option('name');
        $password = $this->option('password');

        $tenant = Tenant::query()->where('slug', $slug)->first();
        if ($tenant === null) {
            $this->error('Tenant não encontrado.');

            return self::FAILURE;
        }

        if ($password === null) {
            $password = $this->secret('Senha');
        }

        if (! is_string($password) || $password === '') {
            $this->error('Senha inválida.');

            return self::FAILURE;
        }

        $schemaManager->setSearchPath($tenant->schema, true);
        $tenantConnection = (string) config('tenancy.tenant_connection', config('database.default'));

        $user = User::on($tenantConnection)->updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password)],
        );

        $this->info(sprintf('Usuário pronto no tenant %s: %s', $tenant->slug, $user->email));

        return self::SUCCESS;
    }
}
