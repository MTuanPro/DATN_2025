<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\ResetPasswordMail;

class ForgotPasswordController extends Controller
{
    /**
     * Hiển thị form quên mật khẩu
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Xử lý gửi email reset mật khẩu
     */
    public function sendResetEmail(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.exists' => 'Email không tồn tại trong hệ thống',
        ]);

        // Tìm user theo email
        $user = User::where('email', $request->email)->first();

        // Kiểm tra trạng thái tài khoản
        if ($user->trang_thai === 'khoa') {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ])->withInput();
        }

        // Tạo mật khẩu mới ngẫu nhiên (8 ký tự)
        $newPassword = Str::random(8);

        // Cập nhật mật khẩu mới cho user
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Gửi email chứa mật khẩu mới
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $newPassword));

            return back()->with('success', 'Mật khẩu mới đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.');
        } catch (\Exception $e) {
            // Log error
            \Log::error('Send reset password email failed: ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'Có lỗi xảy ra khi gửi email. Vui lòng thử lại sau.',
            ])->withInput();
        }
    }
}
