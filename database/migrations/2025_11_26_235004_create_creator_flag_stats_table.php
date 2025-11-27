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
        Schema::create('creator_flag_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedInteger('total_flags')->default(0);
            $table->unsignedInteger('flags_last_30_days')->default(0);
            $table->timestamp('last_flagged_at')->nullable();
            $table->unsignedTinyInteger('escalation_level')->default(0); // 0=none, 1=warned, 2=restricted, 3=banned
            $table->timestamp('escalation_triggered_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('escalation_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_flag_stats');
    }
};
