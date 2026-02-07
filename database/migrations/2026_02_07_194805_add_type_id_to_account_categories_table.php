<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_categories', function (Blueprint $table) {
            // 1=収入, 2=支出（既存データがあるので最初はnullable推奨）
            $table->unsignedTinyInteger('type_id')
                  ->nullable()
                  ->after('id');

            $table->index('type_id');
        });
    }

    public function down(): void
    {
        Schema::table('account_categories', function (Blueprint $table) {
            $table->dropIndex(['type_id']);
            $table->dropColumn('type_id');
        });
    }
};
