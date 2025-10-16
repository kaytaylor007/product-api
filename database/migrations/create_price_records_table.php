<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('price_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('chain_id')->constrained('retail_chains');
            $table->decimal('price_amount', 12, 2);
            $table->string('currency', 3);
            $table->string('unit')->default('each');
            $table->timestamp('effective_at')->useCurrent();
            $table->foreignId('reported_by')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('source_type')->default('mobile_capture');
            $table->string('photo_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id','chain_id']);
            $table->index(['product_id','effective_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('price_records'); }
};
