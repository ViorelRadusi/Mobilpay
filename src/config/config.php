<?php

return [
  'settings' => [
    'environment' =>  'production',  // 'sandbox' or 'production'
    'sandbox_url' =>  'http://sandboxsecure.mobilpay.ro',
    'payment_url' =>  'https://secure.mobilpay.ro',
    'certificates_path' =>  __DIR__.'/../certificates/', // directory which holds 'public.cer' and 'private.key'
    'public_cer'  =>  'public.cer',
    'private_key' =>  'private.key',
    'signature'   =>  '1YBV-4L5L-G9VH-UF18-526U', // 'XXXX-XXXX-XXXX-XXXX-XXXX',
    'confirm_url' =>  'mobilpay/confirm',
    'return_url'  =>  '/',
    'model'       =>  'Ticket', //Model to update whene the confirm_url returns the response
    'status'      =>  'status', // Fielmd in the model that will be updated acording to the returned response
  ]
];
