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
        Schema::create('work_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('work_catalog_id')
                ->constrained()
                ->onDelete('cascade');

            $table->unsignedInteger('order_index')->default(0);
            $table->string('custom_label')->nullable();
            $table->json('custom_config')->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_templates');
    }
};
