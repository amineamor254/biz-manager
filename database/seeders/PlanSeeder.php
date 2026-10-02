<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Free Plan
        Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'features' => config('saas.plans.free.features'),
            ]
        );

        // Basic Plan
        Plan::firstOrCreate(
            ['slug' => 'basic'],
            [
                'name' => 'Basic',
                'price' => 2999,
                'billing_cycle' => 'monthly',
                'features' => config('saas.plans.basic.features'),
            ]
        );

        // Pro Plan
        Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'price' => 9999,
                'billing_cycle' => 'monthly',
                'features' => config('saas.plans.pro.features'),
            ]
        );
    }
}
