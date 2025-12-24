<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dateTime('sche_start')->nullable()->change();
        $table->dateTime('sche_end')->nullable()->change();
        $table->dateTime('sche_done')->nullable()->change();
        $table->dateTime('memo')->nullable()->change();
         $table->dateTime('title')->nullable()->change();
         $table->dateTime('location')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nullable_on_items', function (Blueprint $table) {
            //
        });
    }
};
