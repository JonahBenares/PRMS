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
        Schema::create('pr_head', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->string('company_name')->nullable();
            $table->string('pr_no')->nullable();
            $table->integer('location_id')->default(0);
            $table->string('location_name')->nullable();
            $table->string('date_prepared')->nullable();
            $table->integer('department_id')->default(0);
            $table->string('department_name')->nullable();
            $table->string('department_code')->nullable();
            $table->integer('requestor_id')->default(0);
            $table->string('requestor_name')->nullable();
            $table->integer('urgency_id')->default(0);
            $table->string('urgency_name')->nullable();
            $table->integer('purpose_id')->default(0);
            $table->string('purpose_name')->nullable();
            $table->integer('enduse_id')->default(0);
            $table->string('enduse_name')->nullable();
            $table->string('notes')->nullable();
            $table->integer('user_id')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_head');
    }
};
