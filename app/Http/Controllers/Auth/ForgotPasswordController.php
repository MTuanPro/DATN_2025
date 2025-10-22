<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
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

        // Tạo token reset password
        $token = Str::random(64);

        // Xóa token cũ nếu có
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // Tạo token mới
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Tạo URL reset
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($user->email));

        // Gửi email chứa link reset
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl));

            return back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Có lỗi xảy ra khi gửi email. Vui lòng thử lại sau.',
            ])->withInput();
        }
    }
}
