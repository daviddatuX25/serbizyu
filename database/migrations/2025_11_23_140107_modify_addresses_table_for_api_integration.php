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
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['house_no', 'street', 'barangay', 'town', 'province', 'country']);
            $table->text('full_address')->after('id');
            $table->string('address_hash')->unique()->after('full_address')->nullable();
            $table->string('api_source')->nullable()->after('address_hash');
            $table->string('api_id')->nullable()->after('api_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('town')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('house_no')->nullable();
            $table->dropColumn(['full_address', 'address_hash', 'api_source', 'api_id']);
        });
    }
};
