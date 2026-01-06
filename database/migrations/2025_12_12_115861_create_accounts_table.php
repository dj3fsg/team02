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
            $table->foreignId('user_id')->constrained();
            $table->foreignId('subcategory_id')->constrained();
            $table->foreignId('type_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->date('date')->nullable(true);
            $table->string('title', 255)->nullable(true);
            $table->decimal('amount', total: 12, places: 2)->nullable(true);
            $table->string('memo', 255)->nullable(true);
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
