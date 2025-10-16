<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('gtin', 50)->index();
            $table->string('title', 150);
            $table->text('description', 5000)->nullable();
            $table->string('brand', 70)->nullable();
            $table->string('gpc_cat_id')->nullable();
            $table->string('gpc_cat_full_path')->nullable();
            $table->string('unspsc_cat_code')->nullable();
            $table->string('unspsc_cat_full_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};