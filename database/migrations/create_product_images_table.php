<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploader_id')->constrained('users');
            $table->string('image_url');
            $table->string('thumb_url')->nullable();
            $table->boolean('approved')->default(false);
            $table->boolean('primary')->default(false); // indicates primary image
            $table->timestamps();

            $table->index(['product_id', 'primary']);
        });
    }
    public function down(): void { Schema::dropIfExists('product_images'); }
};