<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Cập nhật email_verified_at cho các tài khoản chưa xác thực
$updated = DB::table('users')
    ->whereNull('email_verified_at')
    ->update(['email_verified_at' => now()]);

echo "✅ Đã cập nhật email_verified_at cho {$updated} tài khoản\n";

// Hiển thị danh sách
$users = DB::table('users')
    ->whereNotNull('email_verified_at')
    ->where('email', 'LIKE', '%.gv@%')
    ->orWhere('email', 'LIKE', '%gv%')
    ->get(['id', 'name', 'email', 'email_verified_at']);

if ($users->count() > 0) {
    echo "\n📋 Danh sách tài khoản giảng viên:\n";
    foreach ($users as $user) {
        echo "- {$user->name} ({$user->email}) - Verified: {$user->email_verified_at}\n";
    }
} else {
    echo "\n❌ Không tìm thấy tài khoản giảng viên\n";
}
