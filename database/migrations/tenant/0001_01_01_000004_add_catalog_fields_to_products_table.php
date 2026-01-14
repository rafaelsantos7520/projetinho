<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('name');
            $table->string('category')->nullable()->index()->after('image_url');
            $table->boolean('is_featured')->default(false)->after('category');
            $table->unsignedInteger('promo_price_cents')->nullable()->after('price_cents');
            $table->unsignedInteger('compare_at_price_cents')->nullable()->after('promo_price_cents');
            $table->decimal('rating_avg', 3, 2)->nullable()->after('description');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'image_url',
                'category',
                'is_featured',
                'promo_price_cents',
                'compare_at_price_cents',
                'rating_avg',
                'rating_count',
            ]);
        });
    }
};
