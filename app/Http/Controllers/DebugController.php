<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SinhVien;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function checkAuth()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'authenticated' => false,
                'message' => 'User not logged in'
            ]);
        }

        // Lấy thông tin vai trò
        $roles = $user->vaiTro->pluck('ma_vai_tro')->toArray();

        // Kiểm tra sinh viên relationship
        $sinhVien = $user->sinhVien;

        // Nếu không có sinh viên, thử tìm trong database
        $sinhVienDirect = null;
        if (!$sinhVien) {
            $sinhVienDirect = DB::table('sinh_vien')
                ->where('user_id', $user->id)
                ->first();
        }
        // Sửa code
        // Kiểm tra xem có users nào khác có sinh viên không
        $sampleUserWithSinhVien = DB::table('sinh_vien')
            ->join('users', 'sinh_vien.user_id', '=', 'users.id')
            ->select('users.id', 'users.email', 'sinh_vien.id as sinh_vien_id', 'sinh_vien.ma_sinh_vien')
            ->first();

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'roles' => $roles,
            'sinhVien' => [
                'via_relationship' => $sinhVien ? [
                    'id' => $sinhVien->id,
                    'ma_sinh_vien' => $sinhVien->ma_sinh_vien,
                    'ho_ten' => $sinhVien->ho_ten,
                ] : null,
                'via_direct_query' => $sinhVienDirect ? (array)$sinhVienDirect : null,
            ],
            'sample_user_with_sinh_vien' => $sampleUserWithSinhVien ? (array)$sampleUserWithSinhVien : null,
            'model_loaded' => class_exists('App\Models\SinhVien'),
            'suggestion' => !$sinhVien && !$sinhVienDirect ?
                'User này không có thông tin sinh viên. Hãy đăng nhập bằng tài khoản sinh viên khác hoặc tạo liên kết user_id trong bảng sinh_vien.'
                : 'OK',
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
