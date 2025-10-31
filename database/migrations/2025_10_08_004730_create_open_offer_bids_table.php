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
            // open offer id, bidder id, service id, proposed price..
            $table->foreignId('open_offer_id')->constrained('open_offers');
            $table->foreignId('bidder_id')->constrained('users');
            $table->foreignId('service_id')->constrained('services');
            $table->decimal('proposed_price', 10, 2);
            $table->timestamps();
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
