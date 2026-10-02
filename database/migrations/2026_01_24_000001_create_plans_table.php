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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Free, Basic, Pro
            $table->string('slug')->unique(); // free, basic, pro
            $table->integer('price'); // in cents, 0 for free
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            
            // Plan features stored as JSON
            $table->json('features')->nullable();
            
            $table->timestamps();
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
