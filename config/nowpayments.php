<?php

return [
    'api_key'     => env('NOWPAYMENTS_API_KEY', 'NOWPAYMENTS_API_KEY_PLACEHOLDER'),
    'ipn_secret'  => env('NOWPAYMENTS_IPN_SECRET', 'NOWPAYMENTS_IPN_SECRET_PLACEHOLDER'),
    'is_sandbox'  => filter_var(env('NOWPAYMENTS_SANDBOX', true), FILTER_VALIDATE_BOOLEAN),

    'base_url'    => filter_var(env('NOWPAYMENTS_SANDBOX', true), FILTER_VALIDATE_BOOLEAN)
        ? 'https://api-sandbox.nowpayments.io/v1'
        : 'https://api.nowpayments.io/v1',
];
