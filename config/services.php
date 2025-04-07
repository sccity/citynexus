<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'workos' => [
        'client_id' => env('WORKOS_CLIENT_ID'),
        'secret' => env('WORKOS_API_KEY'),
        'redirect_url' => env('WORKOS_REDIRECT_URL'),
        'authkit' => [
            'authentication_methods' => env('WORKOS_AUTHKIT_AUTHENTICATION_METHODS', 'email'),
            'allow_authenticated_user_switching' => env('WORKOS_AUTHKIT_ALLOW_AUTHENTICATED_USER_SWITCHING', false),
        ],
    ],
    
    'keycloak' => [    
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'redirect' => env('KEYCLOAK_REDIRECT_URI'),
        'base_url' => env('KEYCLOAK_BASE_URL'),
        'realm' => env('KEYCLOAK_REALM', 'SANTACLARA-DEV'),
        'openid_connect_path' => env('KEYCLOAK_OPENID_CONNECT_PATH', '/protocol/openid-connect'),
        'allowed_redirect_uris' => [
            'https://citynexus-dev.santaclarautah.gov/auth/callback',
            'http://localhost:8000/auth/callback',
            'http://127.0.0.1:8000/auth/callback',
        ],
    ],

    // Add Keycloak Admin Config
    'keycloak_admin' => [
        'base_url' => env('KEYCLOAK_ADMIN_BASE_URL'),
        'realm' => env('KEYCLOAK_ADMIN_REALM'),
        'client_id' => env('KEYCLOAK_ADMIN_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_ADMIN_CLIENT_SECRET'),
        'grant_type' => env('KEYCLOAK_ADMIN_GRANT_TYPE', 'client_credentials'),
        'username' => env('KEYCLOAK_ADMIN_USERNAME'), // Needed only for password grant
        'password' => env('KEYCLOAK_ADMIN_PASSWORD'), // Needed only for password grant
    ],

    'finance_api' => [
        'base_url' => env('FINANCE_API_URL'),
        'token' => env('FINANCE_API_TOKEN'),
    ],

    'kubernetes' => [
        'api_endpoint' => env('KUBERNETES_API_ENDPOINT'),
        'token' => env('KUBERNETES_API_TOKEN'),
        'use_aws_cli' => env('KUBERNETES_USE_AWS_CLI', false),
        'cluster_name' => env('KUBERNETES_CLUSTER_NAME', 'k8s-prd-aws-us-west2'),
    ],

    'airflow' => [
        'api_endpoint' => env('AIRFLOW_API_ENDPOINT'),
        'username' => env('AIRFLOW_USERNAME'),
        'password' => env('AIRFLOW_PASSWORD'),
    ],
];
