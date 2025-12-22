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

            // 外部キーは後で貼る（今はカラムのみ）
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('status_id');

            $table->dateTime('sche_start')->nullable();
            $table->dateTime('sche_end')->nullable();
            $table->dateTime('sche_done')->nullable();

            $table->string('title', 255)->nullable();
            $table->string('memo', 255)->nullable();
            $table->string('location', 255)->nullable();

            $table->timestamps();

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
