<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSinhVienExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinhvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên! Vui lòng liên hệ phòng đào tạo.');
        }

        // Share sinh vien to all views
        view()->share('sinhVien', $sinhVien);

        return $next($request);
    }
}
