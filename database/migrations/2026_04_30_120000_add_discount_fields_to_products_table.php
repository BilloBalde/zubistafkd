<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Ajouter les colonnes pour les réductions si elles n'existent pas
            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->comment('Prix réduit pour les promotions');
            }
            if (!Schema::hasColumn('products', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->nullable()->comment('Pourcentage de réduction (0-100)');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sale_price')) {
                $table->dropColumn('sale_price');
            }
            if (Schema::hasColumn('products', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
        });
    }
};
