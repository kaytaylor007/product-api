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
        Schema::table('price_records', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('reported_by');
            $table->timestamp('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_records', function (Blueprint $table) {
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
