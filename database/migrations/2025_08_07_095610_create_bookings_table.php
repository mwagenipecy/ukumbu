<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// migration
public function up()
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
        $table->date('event_date');
        $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
        $table->decimal('total_amount', 12, 2);
        $table->string('payment_status')->default('unpaid');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
