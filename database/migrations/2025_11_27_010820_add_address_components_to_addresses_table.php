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
            $table->string('street_address')->nullable()->after('label');
            $table->string('barangay')->nullable()->after('street_address');
            $table->string('city')->nullable()->after('barangay');
            $table->string('province')->nullable()->after('city');
            $table->string('region')->nullable()->after('province');
            $table->string('region_name')->nullable()->after('region');
            $table->string('province_name')->nullable()->after('region_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn([
                'street_address',
                'barangay',
                'city',
                'province',
                'region',
                'region_name',
                'province_name',
            ]);
        });
    }
};
