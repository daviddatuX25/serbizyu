<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('payment_method')->default('any')->after('pay_first');
            $table->index('payment_method');
        });

        Schema::table('open_offers', function (Blueprint $table) {
            $table->string('payment_method')->default('any')->after('pay_first');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropColumn('payment_method');
        });

        Schema::table('open_offers', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropColumn('payment_method');
        });
    }
};
