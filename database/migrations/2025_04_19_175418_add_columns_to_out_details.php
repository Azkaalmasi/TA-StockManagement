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
        Schema::table('out_details', function (Blueprint $table) {
            $table->date('expiry_date')->nullable();
            $table->uuid('in_stock_id')->nullable();
            $table->uuid('in_detail_id');

            $table->foreign('in_stock_id')
            ->references('id')->on('in_stocks')
            ->onDelete('cascade');
  
            $table->foreign('in_detail_id')
            ->references('id')->on('in_details')
            ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('out_details', function (Blueprint $table) {
            //
        });
    }
};
