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
        Schema::create('open_offer_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_offer_id')->constrained('open_offers');
            $table->foreignId('bidder_id')->constrained('users');
            $table->foreignId('service_id')->constrained('services')->nullable(); // A bid might not be tied to a specific service
            $table->decimal('amount', 10, 2);
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_offer_bids');
    }
};
