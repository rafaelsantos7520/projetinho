<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('size', 20)->nullable()->after('has_variants'); // PP, P, M, G, GG ou 36, 38, 40...
            $table->string('color', 50)->nullable()->after('size'); // Preto, Branco, Azul...
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['size', 'color']);
        });
    }
};
