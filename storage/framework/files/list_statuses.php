<?php
require 'c:/xampp/htdocs/freeindexer/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/freeindexer/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$statuses = DB::table('order_status')->get();
foreach ($statuses as $status) {
    echo "ID: {$status->id} | Name: {$status->name}\n";
}
