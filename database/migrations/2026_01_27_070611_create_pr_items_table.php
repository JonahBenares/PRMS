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
        Schema::create('pr_items', function (Blueprint $table) {
            $table->id();
            $table->integer('pr_head_id')->default(0);
            $table->string('qty')->nullable();
            $table->string('item_variant_code')->nullable();
            $table->integer('item_variant_id')->default(0);
            $table->string('uom')->nullable();
            $table->string('description')->nullable();
            $table->integer('wh_stocks')->default(0);
            $table->string('date_needed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_items');
    }
};
