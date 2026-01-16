<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PlatformUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário padrão da plataforma (Landlord)
        if (!PlatformUser::where('email', 'admin@plataforma.com')->exists()) {
            PlatformUser::create([
                'name' => 'Admin Plataforma',
                'email' => 'admin@plataforma.com',
                'password' => 'admin123', // O model PlatformUser já faz o Hash automático via casts
            ]);
        }

        // Usuário de teste genérico
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
