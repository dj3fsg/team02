<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('repeats_id')->nullable()->after('id');
            $table->index('repeats_id');
            $table->foreign('repeats_id')
                ->references('id')->on('repeats')
                ->nullOnDelete(); // Repeat削除時はNULLに
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('repeats_id');
        });
    }
};
