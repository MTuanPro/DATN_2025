<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'so_dien_thoai' => 'nullable|string|max:15',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->so_dien_thoai = $request->so_dien_thoai;

        // Xử lý upload avatar
        if ($request->hasFile('anh_dai_dien')) {
            // Xóa ảnh cũ nếu có
            if ($user->anh_dai_dien && Storage::disk('public')->exists($user->anh_dai_dien)) {
                Storage::disk('public')->delete($user->anh_dai_dien);
            }

            $path = $request->file('anh_dai_dien')->store('avatars', 'public');
            $user->anh_dai_dien = $path;
        }

        // Đổi mật khẩu
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // Cập nhật vào bảng tương ứng theo vai trò
        $this->updateRoleTable($user);

        return redirect()->route('profile.show')->with('success', 'Cập nhật thông tin thành công!');
    }

    private function updateRoleTable($user)
    {
        // Lấy vai trò của user
        $role = DB::table('tai_khoan_vai_tro')
            ->join('vai_tro', 'tai_khoan_vai_tro.vai_tro_id', '=', 'vai_tro.id')
            ->where('tai_khoan_vai_tro.tai_khoan_id', $user->id)
            ->first();

        if (!$role) return;

        $data = [
            'ho_ten' => $user->name,
            'email' => $user->email,
            'so_dien_thoai' => $user->so_dien_thoai,
            'anh_dai_dien' => $user->anh_dai_dien,
            'updated_at' => now(),
        ];

        switch ($role->ma_vai_tro) {
            case 'admin':
                DB::table('admin')->where('user_id', $user->id)->update($data);
                break;
            case 'truong_phong_dt':
            case 'nhan_vien_dt':
                DB::table('dao_tao')->where('user_id', $user->id)->update($data);
                break;
            case 'giang_vien':
                DB::table('giang_vien')->where('user_id', $user->id)->update($data);
                break;
            case 'sinh_vien':
                DB::table('sinh_vien')->where('user_id', $user->id)->update($data);
                break;
        }
    }
}
