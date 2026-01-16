<?php

namespace App\Console\Commands;

use App\Models\PlatformUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreatePlatformAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:create-admin {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um usuário administrador da plataforma';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (PlatformUser::where('email', $email)->exists()) {
            $this->error("Usuário com o email {$email} já existe.");
            return 1;
        }

        PlatformUser::create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // O model já tem cast para 'hashed'
        ]);

        $this->info("Usuário administrador {$email} criado com sucesso!");
        return 0;
    }
}
