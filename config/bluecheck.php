<?php

return [
    'base_url' => env('BLUE_CHECK_URL', 'https://customer-api.bluecheck.me/v1'),
    'access_token' => env('BLUE_CHECK_KEY', ''),
    'max_attempts' => env('BLUE_CHECK_MAX_ATTEMPTS', 2),
    'threshold' => env('BLUE_CHECK_THRESHOLD', 0.8)
];
