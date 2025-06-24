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
        Schema::create('damaged_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('damaged_stock_id');
            $table->uuid('product_id');
            $table->string('information');
            $table->integer('quantity');
            $table->date('expiry_date')->nullable();
            $table->uuid('in_stock_id')->nullable();
            $table->uuid('in_detail_id');

            $table->foreign('in_stock_id')
            ->references('id')->on('in_stocks')
            ->onDelete('cascade');
  
            $table->foreign('in_detail_id')
            ->references('id')->on('in_details')
            ->onDelete('cascade');
            
            $table->foreign('damaged_stock_id')->references('id')->on('damaged_stocks')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damaged_details');
    }
};
