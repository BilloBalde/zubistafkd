<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_best')->default(false)->after('price');
            $table->boolean('is_promo')->default(false)->after('is_best');
            $table->decimal('promo_price', 10, 2)->nullable()->after('is_promo');
            $table->decimal('rating', 3, 1)->default(4.5)->after('promo_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_best', 'is_promo', 'promo_price', 'rating']);
        });
    }
};