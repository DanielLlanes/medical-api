<?php

return [

    /*
        |--------------------------------------------------------------------------
        | Custom Application Configuration
        |--------------------------------------------------------------------------
        |
        | This file is for storing custom settings for your application that are
        | not handled by default Laravel configuration files. Here you can centralize
        | business rules, multi-tenancy naming conventions, UI defaults, and
        | specific parameters for your medical ecosystem.
        |
        | Using this file allows you to maintain a clean separation between
        | framework settings and your own logic-driven constants.
        |
    */
    'db_main_prefix' => 'DBs', // El prefijo maestro
    'app_name_slug'  => env('APP_NAME', 'medicalapi'), // El nombre de tu app
    'base_domain'    => env('TENANT_BASE_DOMAIN', 'medical.test'), // <--- Nuevo
    "isProduction" => env('APP_ENV') == 'local' ? false:true,
    'create_tenant_on_registration' => env('CREATE_TENANT_ON_REGISTRATION', false),
];
