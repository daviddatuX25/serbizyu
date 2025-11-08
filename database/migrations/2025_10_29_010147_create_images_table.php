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
        Schema::create('images', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation fields
            $table->morphs('imageable'); // creates imageable_id & imageable_type columns
            $table->string('collection_name')->nullable()->default('default'); // For grouping images (e.g., 'gallery', 'avatar')

            // Image attributes
            $table->string('path');             // e.g., storage path or S3 URL
            $table->string('thumbnail_path')->nullable(); // optional thumbnail
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('order_index')->default(0); // for sorting images
            $table->boolean('is_primary')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};