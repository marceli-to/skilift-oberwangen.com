<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Form Spam Protection Configuration
    |--------------------------------------------------------------------------
    |
    | Min Submit Time: Minimum time in milliseconds before form can be submitted
    |
    */

    'min_submit_time' => env('SPAM_PROTECTION_MIN_SUBMIT_TIME', 3000),
];
