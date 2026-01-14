<?php

namespace App\Console\Commands;

use App\Models\PlatformUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class PlatformMakeAdminCommand extends Command
{
    protected $signature = 'platform:make-admin
        {email : Email do admin}
        {--name=Admin : Nome do admin}
        {--password= : Senha (se omitir, vai pedir)}';

    protected $description = 'Cria um usuário admin da plataforma (landlord).';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = (string) $this->option('name');
        $password = $this->option('password');

        if ($password === null) {
            $password = $this->secret('Senha');
        }

        if (! is_string($password) || $password === '') {
            $this->error('Senha inválida.');
            return self::FAILURE;
        }

        $user = PlatformUser::query()->updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password)],
        );

        $this->info('Admin da plataforma pronto: '.$user->email);

        return self::SUCCESS;
    }
}

