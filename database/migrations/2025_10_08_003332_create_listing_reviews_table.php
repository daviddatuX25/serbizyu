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
        Schema::create('listing_reviews', function (Blueprint $table) {
            $table->id();
            // morphic table
            $table->morphs('listing');
            $table->foreignId('reviewer_id')->constrained('users');
            // $table->foreignId('order_id')->constrained('orders');
            $table->integer('rating');
            $table->string('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_reviews');
    }
};
