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
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing foreign key constraints first
            $table->dropConstrainedForeignId('open_offer_id');
            $table->dropConstrainedForeignId('open_offer_bid_id');

            // Make columns nullable
            $table->foreignId('open_offer_id')->nullable()->constrained('open_offers')->onDelete('set null')->change();
            $table->foreignId('open_offer_bid_id')->nullable()->constrained('open_offer_bids')->onDelete('set null')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert to non-nullable and non-nullable foreign keys
            // This assumes a non-nullable state by default after dropping constraints
            $table->dropConstrainedForeignId('open_offer_id');
            $table->dropConstrainedForeignId('open_offer_bid_id');

            $table->foreignId('open_offer_id')->constrained('open_offers')->onDelete('cascade')->change();
            $table->foreignId('open_offer_bid_id')->constrained('open_offer_bids')->onDelete('cascade')->change();
        });
    }
};
