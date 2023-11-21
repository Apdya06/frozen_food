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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index('user_id_foreign');
            $table->string('brand_name', 100);
            $table->string('product_name', 100);
            $table->enum('category', ['sayur', 'buah', 'olahan_ayam', 'olahan_sapi', 'seafood','frozen_misc'])->default('frozen_misc');
            $table->enum('origin', array('lokal', 'import'))->default('lokal');
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
