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
        Schema::table('services', function (Blueprint $table) {
            // Add price range fields
            $table->decimal('price_min', 12, 2)->nullable()->after('price');
            $table->decimal('price_max', 12, 2)->nullable()->after('price_min');
            
            // Make price nullable since we'll use ranges, and keep pricing_model
            $table->decimal('price', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_min', 'price_max']);
            $table->decimal('price', 12, 2)->nullable(false)->change();
        });
    }
};