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
        Schema::create('in_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('in_stock_id');
            $table->uuid('product_id');
            $table->integer('quantity');

            $table->foreign('in_stock_id')->references('id')->on('in_stocks')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_in');
    }
};
