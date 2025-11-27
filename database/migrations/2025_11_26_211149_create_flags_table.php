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
        Schema::create('flags', function (Blueprint $table) {
            $table->id();
            $table->morphs('flaggable'); // polymorphic: flaggable_id and flaggable_type (creates index automatically)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who reported
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // who reviewed
            $table->string('category'); // spam, inappropriate, fraud, etc.
            $table->text('reason'); // why it was flagged
            $table->text('evidence')->nullable(); // additional details/context
            $table->string('status')->default('pending'); // pending, approved, rejected, resolved
            $table->text('admin_notes')->nullable(); // notes from admin review
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flags');
    }
};
