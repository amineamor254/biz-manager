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
        // Add workspace_id to clients
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            
            // Additional fields
            $table->string('contact_person')->nullable()->after('name');
            $table->string('tax_id')->nullable()->after('contact_person');
            $table->text('billing_address')->nullable()->after('tax_id');
            $table->string('status')->default('active')->after('billing_address'); // active, inactive, archived
            
            $table->index(['workspace_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
            $table->dropIndex(['workspace_id', 'status']);
            $table->dropColumn([
                'workspace_id',
                'contact_person',
                'tax_id',
                'billing_address',
                'status',
            ]);
        });
    }
};
