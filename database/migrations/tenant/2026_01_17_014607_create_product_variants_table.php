<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de opções de variação do produto (ex: "Cor", "Tamanho")
        // Cada produto pode ter suas próprias opções
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // "Cor", "Tamanho"
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['product_id', 'sort_order']);
        });

        // Tabela de valores de cada opção (ex: "Azul", "M", "42")
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')->constrained('product_options')->onDelete('cascade');
            $table->string('value'); // "Azul", "M", "42"
            $table->unsignedInteger('price_modifier_cents')->nullable(); // +R$10,00 ou preço absoluto
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['option_id', 'sort_order']);
        });

        // Adicionar campo has_variants na tabela products
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_variants')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('has_variants');
        });
        
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_options');
    }
};
