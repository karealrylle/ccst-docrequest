<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DocumentRequest;

$student = User::where('role', 'student')->first();
$registrar = User::where('role', 'registrar')->first();
$docReq = DocumentRequest::first();

echo "STUDENT_ID:" . ($student->id ?? 'null') . "\n";
echo "REGISTRAR_ID:" . ($registrar->id ?? 'null') . "\n";
echo "DOC_REQ_ID:" . ($docReq->id ?? 'null') . "\n";
