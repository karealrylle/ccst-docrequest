<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$notif = DB::table('notifications')->latest()->first();

if ($notif) {
    echo "ID: " . $notif->id . "\n";
    echo "DATA RAW: " . $notif->data . "\n";
    $decoded = json_decode($notif->data, true);
    echo "DECODED: " . print_r($decoded, true) . "\n";
} else {
    echo "No notifications found.\n";
}
