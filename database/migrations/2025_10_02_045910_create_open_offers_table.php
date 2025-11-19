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
        Schema::create('open_offers', function (Blueprint $table) {
            // 'title', 'description', 'budget', 'pay_first', 'category_id', 'creator_id', 'workflow_template_id', 'address_id'
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->decimal('budget', 10, 2);
            $table->boolean('pay_first');
            $table->boolean('fulfilled');
            $table->foreignId('address_id')->constrained('addresses');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('workflow_template_id')->constrained()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_offers');
    }
};
