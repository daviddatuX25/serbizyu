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
        Schema::table('work_catalogs', function (Blueprint $table) {
            // Add category_id foreign key (nullable)
            $table->unsignedBigInteger('category_id')->nullable()->after('description');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');

            // Drop old config field if it exists
            if (Schema::hasColumn('work_catalogs', 'config')) {
                $table->dropColumn('config');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_catalogs', function (Blueprint $table) {
            // Add back config field
            $table->json('config')->nullable();

            // Drop new field
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
