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
        Schema::table('venues', function (Blueprint $table) {
            // Add price range fields
            $table->decimal('price_min', 12, 2)->nullable()->after('base_price');
            $table->decimal('price_max', 12, 2)->nullable()->after('price_min');
            $table->string('price_type', 50)->default('per_event')->after('price_max'); // per_event, per_hour, per_day
            
            // Make base_price nullable since we'll use ranges
            $table->decimal('base_price', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['price_min', 'price_max', 'price_type']);
            $table->decimal('base_price', 12, 2)->nullable(false)->change();
        });
    }
};