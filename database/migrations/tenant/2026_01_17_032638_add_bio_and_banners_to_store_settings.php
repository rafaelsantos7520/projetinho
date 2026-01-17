<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->text('biography')->nullable()->after('facebook_url');
            $table->string('banner_1_url', 2048)->nullable()->after('biography');
            $table->string('banner_2_url', 2048)->nullable()->after('banner_1_url');
            $table->string('banner_3_url', 2048)->nullable()->after('banner_2_url');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['biography', 'banner_1_url', 'banner_2_url', 'banner_3_url']);
        });
    }
};
