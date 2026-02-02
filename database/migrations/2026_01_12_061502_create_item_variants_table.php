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
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id')->default(0);
            $table->string('variant_item_code')->nullable();
            $table->string('brand')->nullable();
            $table->string('uom')->nullable();
            $table->string('type')->nullable();
            $table->string('model')->nullable();
            $table->string('part_no')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('img1')->nullable();
            $table->string('img2')->nullable();
            $table->string('img3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};
