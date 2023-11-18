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
            $table->string('product_name', 100);
            $table->enum('category', ['sayur', 'buah', 'olahan ayan', 'olahan sapi', 'seafood', 'frozen'])->default('frozen');
            $table->enum('shelf_life', ['3 bulan', '6 bulan', '9 bulan', '12 bulan'])->default('3 bulan');
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->double('review', 10)->nullable();
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
