<?php
require 'c:/xampp/htdocs/freeindexer/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/freeindexer/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$uid = 24;
$user = DB::table('users')->find($uid);
if (!$user) {
    die("User $uid not found.\n");
}
echo "User: {$user->email} (ID: {$user->id})\n";

foreach (['indexer_points', 'bg_indexer_points', 'backlinks_points'] as $table) {
    $record = DB::table($table)->where('uid', $uid)->first();
    if ($record) {
        echo "Table: $table | Points: {$record->points} | Used: {$record->used}\n";
    } else {
        echo "Table: $table | No record found\n";
    }
}
