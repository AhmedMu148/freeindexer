<?php
require 'c:/xampp/htdocs/freeindexer/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/freeindexer/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== pym_payments (last 5) ===\n";
$payments = DB::table('pym_payments')->orderBy('id', 'desc')->limit(5)->get();
foreach ($payments as $payment) {
    echo "ID: {$payment->id} | User: {$payment->uid} | Plan: {$payment->plan_id} | Amount: {$payment->amount} | Status: {$payment->status} | Hash: {$payment->payment_hash} | Updated: {$payment->updated_at}\n";
}

echo "\n=== webhook_events (last 5) ===\n";
$events = DB::table('webhook_events')->orderBy('id', 'desc')->limit(5)->get();
foreach ($events as $event) {
    echo "ID: {$event->id} | Event: {$event->event_type} | Status: {$event->status} | Error: {$event->error_message} | Created: {$event->created_at}\n";
}
