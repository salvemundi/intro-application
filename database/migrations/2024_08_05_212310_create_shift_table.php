<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('shift_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('shift_leader');
            $table->foreign('shift_leader')->references('id')->on('participants');
            $table->string('color');
            $table->timestamps();
        });
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('max_participants');
            $table->uuid('shift_cat');
            $table->foreign('shift_cat')->references('id')->on('shift_categories');
            $table->timestamps();
        });
        Schema::create('shift_participants', function (Blueprint $table) {
            $table->uuid('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->uuid('shift_worker');
            $table->foreign('shift_worker')->references('id')->on('participants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_participants');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('shift_categories');
    }
};
