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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            
            // Invoice tracking
            $table->string('invoice_number')->nullable()->after('workspace_id'); // Unique per workspace
            $table->string('status')->default('draft')->after('invoice_number'); // draft, sent, paid, overdue, cancelled
            $table->date('due_date')->nullable()->after('date');
            $table->string('payment_method')->nullable()->after('due_date'); // credit_card, bank_transfer, cash, check
            $table->dateTime('payment_received_at')->nullable()->after('payment_method');
            $table->text('notes')->nullable()->after('payment_received_at');
            $table->text('terms')->nullable()->after('notes');
            
            // Unique invoice_number per workspace
            $table->unique(['workspace_id', 'invoice_number']);
            $table->index(['workspace_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
            $table->dropUnique(['workspace_id', 'invoice_number']);
            $table->dropIndex(['workspace_id', 'status']);
            $table->dropColumn([
                'workspace_id',
                'invoice_number',
                'status',
                'due_date',
                'payment_method',
                'payment_received_at',
                'notes',
                'terms',
            ]);
        });
    }
};
