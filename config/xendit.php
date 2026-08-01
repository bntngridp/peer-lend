<?php

return [
    'secret_key'    => env('XENDIT_SECRET_KEY', 'xnd_development_placeholderkey'),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', 'xnd_webhook_token_placeholder'),
    'is_production' => filter_var(env('XENDIT_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),

    'disbursement_url' => 'https://api.xendit.co/disbursements',
    'base_url'         => 'https://api.xendit.co',
];
