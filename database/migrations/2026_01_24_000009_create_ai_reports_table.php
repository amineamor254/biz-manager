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
        Schema::create('ai_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('type'); // sales_summary, top_products, client_analysis, trend_forecast
            $table->string('title');
            $table->text('description')->nullable();
            
            // The actual AI-generated report content
            $table->json('report_data');
            
            // Time period covered
            $table->date('period_start');
            $table->date('period_end');
            
            // Cache control
            $table->dateTime('expires_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['workspace_id', 'type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_reports');
    }
};
