<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
        });

        // Migrar dados existentes
        $products = DB::table('products')->whereNotNull('category')->where('category', '!=', '')->get();
        foreach ($products as $product) {
            $catId = DB::table('categories')->where('name', $product->category)->value('id');
            if (! $catId) {
                $catId = DB::table('categories')->insertGetId([
                    'name' => $product->category,
                    'slug' => \Illuminate\Support\Str::slug($product->category),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('products')->where('id', $product->id)->update(['category_id' => $catId]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

        // Restaurar dados (aproximado)
        $products = DB::table('products')->whereNotNull('category_id')->get();
        foreach ($products as $product) {
            $catName = DB::table('categories')->where('id', $product->category_id)->value('name');
            if ($catName) {
                DB::table('products')->where('id', $product->id)->update(['category' => $catName]);
            }
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
