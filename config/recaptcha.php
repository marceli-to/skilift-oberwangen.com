<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v3 Configuration
    |--------------------------------------------------------------------------
    |
    | Site Key: Used in frontend to render reCAPTCHA
    | Secret Key: Used in backend to verify reCAPTCHA response
    | Minimum Score: Threshold for passing verification (0.0 to 1.0)
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'min_score' => env('RECAPTCHA_MIN_SCORE', 0.5),
    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
];
