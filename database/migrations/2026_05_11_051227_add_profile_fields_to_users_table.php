<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name');
            $table->string('bio', 300)->nullable()->after('avatar');
            $table->json('notification_preferences')
                  ->nullable()
                  ->after('bio');
            // e.g. {"order_updates": true, "promotions": false}
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'notification_preferences']);
        });
    }
};