<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id')->nullable()->unique()->index(); // guest cart
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // auth cart
            $table->timestamps();

            // One DB cart per user
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};