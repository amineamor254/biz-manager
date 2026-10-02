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
        Schema::table('users', function (Blueprint $table) {
            // Add workspace context columns
            $table->foreignId('current_workspace_id')->nullable()->after('id');
            
            // Update existing password to nullable (for OAuth future compatibility)
            $table->string('password')->nullable()->change();
            
            // Role at platform level
            $table->string('role')->default('user')->after('password'); // admin, user
            
            // User meta
            $table->string('verification_code')->nullable()->after('email_verified_at');
            $table->dateTime('last_login_at')->nullable()->after('remember_token');
            $table->json('settings')->nullable()->after('last_login_at'); // theme, preferences
            
            $table->index('role');
            $table->index('current_workspace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'current_workspace_id',
                'role',
                'verification_code',
                'last_login_at',
                'settings',
            ]);
        });
    }
};
