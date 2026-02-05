<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_add_repeats_id_to_items_table.php
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('repeats_id')->nullable()->after('id');
            $table->index('repeats_id');
            $table->foreign('repeats_id')
                ->references('id')->on('repeats')
                ->nullOnDelete(); // Repeat削除時はNULLに
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['repeats_id']);
            $table->dropIndex(['repeats_id']);
            $table->dropColumn('repeats_id');
        });
    }

};
