<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lên lịch kiểm tra cảnh báo điểm danh
// Chạy vào mỗi Chủ Nhật lúc 20:00
Schedule::command('attendance:check-warnings')
    ->weeklyOn(0, '20:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->emailOutputOnFailure('admin@example.com');
