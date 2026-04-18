<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('brand_id');
            $table->index('category_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('user_prices', function (Blueprint $table) {
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('user_prices', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });
    }
};
