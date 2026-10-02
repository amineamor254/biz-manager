<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create demo workspace for admin
        $workspace = Workspace::firstOrCreate(
            ['user_id' => $admin->id, 'slug' => 'admin-demo'],
            [
                'name' => 'Admin Demo Workspace',
                'timezone' => 'UTC',
                'currency' => 'USD',
                'is_active' => true,
            ]
        );

        // Add admin to workspace
        WorkspaceUser::firstOrCreate(
            ['workspace_id' => $workspace->id, 'user_id' => $admin->id],
            [
                'role' => 'owner',
                'accepted_at' => now(),
            ]
        );

        // Create Pro subscription for demo
        $proPlan = Plan::where('slug', 'pro')->first();
        if ($proPlan) {
            Subscription::firstOrCreate(
                ['workspace_id' => $workspace->id],
                [
                    'plan_id' => $proPlan->id,
                    'status' => 'active',
                ]
            );
        }

        // Set current workspace
        $admin->update(['current_workspace_id' => $workspace->id]);
    }
}
