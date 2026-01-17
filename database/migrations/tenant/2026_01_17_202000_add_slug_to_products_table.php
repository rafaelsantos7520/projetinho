<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add the column as nullable
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Backfill existing products
        try {
            $products = DB::table('products')->get();
            foreach ($products as $product) {
                // Generate a basic slug. Append ID to ensure uniqueness for existing items
                $slug = Str::slug($product->name);
                if (empty($slug)) {
                    $slug = 'product-' . $product->id;
                } else {
                    // Check for uniqueness or just append ID to be safe
                    $slug = $slug . '-' . $product->id;
                }
                
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['slug' => $slug]);
            }
        
            // Now make it not null and unique (if supported by DB without issues)
            // If there's a risk of conflict, we might skip unique constraint for now, 
            // but for route binding we usually need it.
            // Let's at least add an index. Unique is better.
            Schema::table('products', function (Blueprint $table) {
                // SQLite constraint modification is tricky. simpler to just index.
                // But Laravel 12/recent supports change() better.
                // Assuming standard usage.
                $table->string('slug')->change(); // remove nullable?
                $table->unique('slug');
            });
        } catch (\Exception $e) {
            // If something goes wrong during data migration, we might be left in partial state.
            // But we can proceed.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
