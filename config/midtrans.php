<?php

return [
    'server_key'    => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-placeholderkey'),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-placeholderkey'),
    'is_production' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),
    
    'snap_url' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN)
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',
];
