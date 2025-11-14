<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===== NOTIFICATION JOBS =====
// Kiểm tra học phí sắp đến hạn và quá hạn
// Chạy mỗi ngày lúc 8:00 sáng
Schedule::job(new \App\Jobs\CheckTuitionDeadlineJob())
    ->dailyAt('08:00')
    ->timezone('Asia/Ho_Chi_Minh');

// Kiểm tra và nhắc nhở lịch thi sắp tới
// Chạy mỗi ngày lúc 7:00 sáng
Schedule::job(new \App\Jobs\CheckExamReminderJob())
    ->dailyAt('07:00')
    ->timezone('Asia/Ho_Chi_Minh');

// ===== ATTENDANCE JOBS =====
// Lên lịch kiểm tra cảnh báo điểm danh
// Chạy vào mỗi Chủ Nhật lúc 20:00
Schedule::command('attendance:check-warnings')
    ->weeklyOn(0, '20:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->emailOutputOnFailure('admin@example.com');

// ===== CHATBOT LEARNING JOBS =====
// Phân tích feedback và điều chỉnh độ ưu tiên knowledge base
// Chạy mỗi ngày lúc 2:00 sáng
Schedule::command('chatbot:analyze-feedback')
    ->dailyAt('02:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->runInBackground();

// Tạo báo cáo tuần cho chatbot
// Chạy vào mỗi thứ Hai lúc 9:00 sáng
Schedule::command('chatbot:weekly-report')
    ->weeklyOn(1, '09:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->runInBackground();
