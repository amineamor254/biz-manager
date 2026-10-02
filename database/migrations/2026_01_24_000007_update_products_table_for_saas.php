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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            
            // Enhanced product data
            $table->text('description')->nullable()->after('name');
            $table->string('sku')->nullable()->unique()->after('description');
            $table->integer('stock_level')->default(0)->after('quantity');
            $table->string('category')->nullable()->after('stock_level');
            $table->decimal('margin', 5, 2)->default(0)->after('price'); // Profit margin %
            $table->string('status')->default('active')->after('margin'); // active, inactive, discontinued
            
            $table->index(['workspace_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
            $table->dropIndex(['workspace_id', 'status']);
            $table->dropUnique(['sku']);
            $table->dropColumn([
                'workspace_id',
                'description',
                'sku',
                'stock_level',
                'category',
                'margin',
                'status',
            ]);
        });
    }
};
