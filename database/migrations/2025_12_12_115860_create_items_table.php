<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('subcategory_id')->constrained();
            $table->foreignId('type_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->dateTime('sche_start', $precision = 0)->nullable(true); //precision=0はミリ秒以下切り捨て
            $table->dateTime('sche_end', $precision = 0)->nullable(true);
            $table->dateTime('sche_done', $precision = 0)->nullable(true);
            $table->string('title', 255)->nullable(true);
            $table->string('memo', 255)->nullable(true);
            $table->string('location', 255)->nullable(true);
            $table->timestamps(); //created/updated

            // インデックス（パフォーマンス用）
            $table->index('user_id');
            $table->index('subcategory_id');
            $table->index('type_id');
            $table->index('status_id');
            $table->index('sche_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
