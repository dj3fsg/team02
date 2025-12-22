<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // 外部キーは後で貼る（今はカラムだけ作る）
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('status_id');

            $table->date('date'); // 必須にするならこのまま
            $table->string('title', 255)->nullable(); // タイトルは任意ならnullable
            $table->decimal('amount', 8, 2);          // 金額
            $table->string('memo', 255)->nullable();  // メモは任意ならnullable

            $table->timestamps();

            // 便利なインデックス（重くないし入れとくと◎）
            $table->index('user_id');
            $table->index('date');
            $table->index('subcategory_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
