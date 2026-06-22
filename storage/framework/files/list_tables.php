<?php
require 'c:/xampp/htdocs/freeindexer/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/freeindexer/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $array = (array)$table;
    echo array_values($array)[0] . "\n";
}
