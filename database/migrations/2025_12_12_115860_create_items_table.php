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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('subcategory_id')->constrained();
            $table->foreignId('type_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->dateTime('sche_start', $precision = 0); //precision=0はミリ秒以下切り捨て
            $table->dateTime('sche_end', $precision = 0);
            $table->dateTime('sche_done', $precision = 0);
            $table->string('title', 255);
            $table->string('memo', 255);
            $table->string('location', 255);
            $table->timestamps(); //created/updated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
