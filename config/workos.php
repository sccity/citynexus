<?php

return [
    // WorkOS API Key
    "api_key" => env("WORKOS_API_KEY"),

    // WorkOS Client ID
    "client_id" => env("WORKOS_CLIENT_ID"),

    // WorkOS base API URL
    "api_base_url" => null,
    
    // AuthKit configuration
    "authkit" => [
        // Authentication methods to display on the login page
        // Possible values: "sso", "email", "oauth", "magic_auth"
        "authentication_methods" => env("WORKOS_AUTHKIT_AUTHENTICATION_METHODS", "email"),
        
        // Whether to allow authenticated users to switch accounts
        "allow_authenticated_user_switching" => env("WORKOS_AUTHKIT_ALLOW_AUTHENTICATED_USER_SWITCHING", false),
    ],
];
