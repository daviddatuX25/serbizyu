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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('open_offer_id')->constrained('open_offers');
            $table->foreignId('open_offer_bid_id')->constrained('open_offer_bids');
            $table->decimal('price', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('status');
            $table->string('payment_status');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
