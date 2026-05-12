<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand', 100)->nullable()->after('name')->index();
            $table->json('tags')->nullable()->after('meta_description');
            // tags: ["trending","new","bestseller","sale","exclusive"]
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'tags']);
        });
    }
};