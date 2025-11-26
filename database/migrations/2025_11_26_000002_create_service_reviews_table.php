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
        Schema::create('service_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->integer('rating')->comment('Rating from 1-5');
            $table->string('title')->nullable();
            $table->text('comment');
            $table->json('tags')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('reviewer_id');
            $table->index('service_id');
            $table->index('order_id');
            $table->index('rating');
            $table->index('is_verified_purchase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reviews');
    }
};
