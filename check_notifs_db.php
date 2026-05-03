<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$notifs = DB::table('notifications')->latest()->take(10)->get();

foreach ($notifs as $n) {
    echo "ID: " . $n->id . " | DATA: " . $n->data . " | CREATED: " . $n->created_at . "\n";
}
