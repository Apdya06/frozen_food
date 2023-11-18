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
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('manufacturer_name', 100)->unique();
            $table->string('brand_name', 100);
            $table->year('founded_year');
            $table->enum('origin', array('lokal', 'import'))->default('lokal');
            $table->string('country_origin', 100);
            $table->string('address', 100);
            $table->string('email', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
