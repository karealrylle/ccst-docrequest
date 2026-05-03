<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$types = DB::table('notifications')->select('notifiable_type')->distinct()->get();
echo "TYPES IN DB:\n";
foreach ($types as $t) {
    echo "- " . $t->notifiable_type . "\n";
}

echo "ACTUAL USER CLASS: " . get_class(\App\Models\User::first()) . "\n";
