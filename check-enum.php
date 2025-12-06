<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$result = DB::select('SHOW COLUMNS FROM diem_danh WHERE Field = "trang_thai"');
echo $result[0]->Type;
