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
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('event_time')->nullable()->after('event_date');
            $table->integer('guest_count')->nullable()->after('event_time');
            $table->text('special_requests')->nullable()->after('guest_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['event_time', 'guest_count', 'special_requests']);
        });
    }
};