<?php
return [
  'mode'     => env('PAYPAL_MODE', 'sandbox'), // sandbox | live
  'business' => env('PAYPAL_BUSINESS_EMAIL'),
  'return'   => env('PAYPAL_RETURN_URL'),
  'cancel'   => env('PAYPAL_CANCEL_URL'),
  'ipn'      => env('PAYPAL_IPN_URL'),
  'currency' => env('PAYPAL_CURRENCY', 'USD'),

  'webscr' => env('PAYPAL_MODE', 'sandbox') === 'live'
    ? 'https://www.paypal.com/cgi-bin/webscr'
    : 'https://www.sandbox.paypal.com/cgi-bin/webscr',

  'ipn_verify' => env('PAYPAL_MODE', 'sandbox') === 'live'
    ? 'https://ipnpb.paypal.com/cgi-bin/webscr'
    : 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr',
];
