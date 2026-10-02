<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SaaS Configuration
    |--------------------------------------------------------------------------
    */

    'plans' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'billing_cycle' => 'monthly',
            'features' => [
                'max_workspaces' => 1,
                'max_clients' => 10,
                'max_invoices_per_month' => 50,
                'ai_features' => false,
                'api_access' => false,
                'custom_domain' => false,
                'priority_support' => false,
                'team_members' => 1,
            ],
        ],

        'basic' => [
            'name' => 'Basic',
            'price' => 2999, // $29.99 in cents
            'billing_cycle' => 'monthly',
            'features' => [
                'max_workspaces' => 3,
                'max_clients' => 100,
                'max_invoices_per_month' => 500,
                'ai_features' => false,
                'api_access' => false,
                'custom_domain' => false,
                'priority_support' => false,
                'team_members' => 3,
            ],
        ],

        'pro' => [
            'name' => 'Pro',
            'price' => 9999, // $99.99 in cents
            'billing_cycle' => 'monthly',
            'features' => [
                'max_workspaces' => null, // unlimited
                'max_clients' => null,
                'max_invoices_per_month' => null,
                'ai_features' => true,
                'api_access' => true,
                'custom_domain' => true,
                'priority_support' => true,
                'team_members' => null, // unlimited
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    */

    'stripe' => [
        'key' => env('STRIPE_PUBLIC_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial Period (days)
    |--------------------------------------------------------------------------
    */

    'trial_days' => env('SAAS_TRIAL_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (requests per day by plan)
    |--------------------------------------------------------------------------
    */

    'rate_limits' => [
        'free' => 100,
        'basic' => 1000,
        'pro' => null, // unlimited
    ],
];
