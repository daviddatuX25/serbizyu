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
        Schema::table('open_offers', function (Blueprint $table) {
            $table->timestamp('deadline')->nullable()->after('budget');
            $table->enum('status', ['pending', 'open', 'closed', 'fulfilled', 'cancelled'])->default('open')->after('fulfilled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_offers', function (Blueprint $table) {
            $table->dropColumn('deadline');
            $table->dropColumn('status');
        });
    }
};
