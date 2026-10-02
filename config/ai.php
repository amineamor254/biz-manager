<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Service Configuration
    |--------------------------------------------------------------------------
    */

    'provider' => env('AI_PROVIDER', 'openai'), // openai, claude, etc.

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
        'organization_id' => env('OPENAI_ORG_ID'),
    ],

    'claude' => [
        'key' => env('CLAUDE_API_KEY'),
        'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Generation
    |--------------------------------------------------------------------------
    */

    'reports' => [
        'cache_duration' => 7, // days
        'generate_on_demand' => true,
        'enable_scheduling' => true, // daily/weekly reports
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    */

    'features' => [
        'sales_reports' => true,
        'product_insights' => true,
        'client_analysis' => true,
        'trend_forecasting' => true,
        'recommendations' => true,
    ],
];
