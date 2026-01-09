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
    "bd_prefix" => env('APP_NAME', 'Medical API'),
    "isProduction" => env('APP_ENV') == 'local' ? false:true
];