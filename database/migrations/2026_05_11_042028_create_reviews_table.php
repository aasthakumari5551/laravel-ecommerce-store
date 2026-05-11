<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()    // verified purchase badge
                  ->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating');        // 1–5
            $table->string('title', 150)->nullable();
            $table->text('body')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('rejection_reason')->nullable(); // set on rejection
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // One review per user per product
            $table->unique(['product_id', 'user_id']);
            $table->index(['product_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};