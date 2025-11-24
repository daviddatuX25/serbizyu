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
        Schema::create('work_instance_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_instance_id')->constrained('work_instances');
            $table->foreignId('work_template_id')->constrained('work_templates');
            $table->integer('step_index');
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_instance_steps');
    }
};
