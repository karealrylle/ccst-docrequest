<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DocumentRequest;

$cancelled = DocumentRequest::where('status', 'cancelled')->get();
echo "COUNT: " . $cancelled->count() . "\n";
foreach ($cancelled as $c) {
    echo "ID: " . $c->id . " | REF: " . $c->reference_number . " | STUDENT: " . $c->full_name . "\n";
}
