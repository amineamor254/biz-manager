<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('workspace_roles')) {
            return;
        }

        foreach (Schema::getIndexes('workspace_roles') as $index) {
            if ($index['unique'] && $index['columns'] === ['name']) {
                Schema::table('workspace_roles', fn (Blueprint $table) => $table->dropUnique($index['name']));
            }
        }

        if (!Schema::hasIndex('workspace_roles', ['workspace_id', 'name'], 'unique')) {
            Schema::table('workspace_roles', fn (Blueprint $table) => $table->unique(['workspace_id', 'name']));
        }
    }

    public function down(): void
    {
        Schema::table('workspace_roles', function (Blueprint $table) {
            $table->dropUnique(['workspace_id', 'name']);
            $table->unique('name');
        });
    }
};