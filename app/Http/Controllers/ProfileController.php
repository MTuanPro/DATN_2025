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
        $role = $this->getUserRole($user);

        // Load dữ liệu tùy theo vai trò
        $data = ['user' => $user, 'role' => $role];

        switch ($role) {
            case 'sinh_vien':
                // Refresh relationship để lấy dữ liệu mới nhất
                $user->load('sinhVien');
                $sinhVien = $user->sinhVien;
                $data['sinhVien'] = $sinhVien;
                return view('profile.sinh-vien', $data);

            case 'giang_vien':
                // Load relationship để đảm bảo có dữ liệu mới nhất
                $user->load('giangVien');
                $giangVien = $user->giangVien;
                if (!$giangVien) {
                    return redirect()->route('giangvien.dashboard')
                        ->with('error', 'Không tìm thấy thông tin giảng viên.');
                }
                $data['giangVien'] = $giangVien;
                return view('profile.giang-vien', $data);

            case 'truong_phong_dt':
            case 'nhan_vien_dt':
                $daoTao = $user->fresh()->daoTao;
                $data['daoTao'] = $daoTao;
                return view('profile.dao-tao', $data);

            case 'admin':
                $admin = $user->fresh()->admin;
                $data['admin'] = $admin;
                return view('profile.admin', $data);

            default:
                return view('profile.show', $data);
        }
    }

    private function getUserRole($user)
    {
        $role = DB::table('tai_khoan_vai_tro')
            ->join('vai_tro', 'tai_khoan_vai_tro.vai_tro_id', '=', 'vai_tro.id')
            ->where('tai_khoan_vai_tro.tai_khoan_id', $user->id)
            ->first();

        return $role ? $role->ma_vai_tro : null;
    }


    public function update(Request $request)
    {
        $user = Auth::user();
        $role = $this->getUserRole($user);

        // Validation cơ bản
        $rules = [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'so_dien_thoai' => 'nullable|string|max:15',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ];

        // Thêm validation tùy vai trò
        switch ($role) {
            case 'sinh_vien':
                $rules['ngay_sinh'] = 'nullable|date';
                $rules['gioi_tinh'] = 'nullable|in:nam,nu,khac';
                $rules['so_nha_duong'] = 'nullable|string|max:255';
                $rules['phuong_xa'] = 'nullable|string|max:255';
                $rules['quan_huyen'] = 'nullable|string|max:255';
                $rules['tinh_thanh'] = 'nullable|string|max:255';
                $rules['can_cuoc_cong_dan'] = 'nullable|string|max:20';
                break;

            case 'giang_vien':
                $rules['ngay_sinh'] = 'nullable|date';
                $rules['gioi_tinh'] = 'nullable|in:Nam,Nữ,Khác';
                $rules['dia_chi'] = 'nullable|string|max:500';
                $rules['chuyen_mon'] = 'nullable|string|max:255';
                $rules['ngay_vao_truong'] = 'nullable|date';
                break;

            case 'truong_phong_dt':
            case 'nhan_vien_dt':
            case 'admin':
                $rules['ngay_sinh'] = 'nullable|date';
                $rules['gioi_tinh'] = 'nullable|in:Nam,Nữ,Khác';
                $rules['dia_chi'] = 'nullable|string|max:500';
                break;
        }

        $request->validate($rules);

        // Xử lý upload avatar
        $avatarPath = null;
        if ($request->hasFile('anh_dai_dien')) {
            // Lấy ảnh cũ từ bảng tương ứng để xóa
            $anhCu = $user->anh_dai_dien;
            if ($anhCu && Storage::disk('public')->exists($anhCu)) {
                Storage::disk('public')->delete($anhCu);
            }

            $avatarPath = $request->file('anh_dai_dien')->store('avatars', 'public');
        }

        // Đổi mật khẩu nếu có nhập mật khẩu mới
        if ($request->filled('new_password')) {
            // Kiểm tra mật khẩu hiện tại
            if (!$request->filled('current_password')) {
                return back()->withErrors(['current_password' => 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu mới.']);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
        }

        // Cập nhật vào bảng tương ứng theo vai trò
        $this->updateRoleTable($user, $request, $role, $avatarPath);

        return redirect()->route('profile.show')->with('success', 'Cập nhật thông tin thành công!');
    }

    private function updateRoleTable($user, $request, $role, $avatarPath = null)
    {
        $data = [
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'updated_at' => now(),
        ];

        // Chỉ thêm số điện thoại nếu có giá trị
        if ($request->filled('so_dien_thoai')) {
            $data['so_dien_thoai'] = $request->so_dien_thoai;
        }

        // Chỉ thêm ảnh đại diện nếu có upload mới
        if ($avatarPath) {
            $data['anh_dai_dien'] = $avatarPath;
        }

        // Thêm dữ liệu tùy vai trò
        switch ($role) {
            case 'sinh_vien':
                // Debug
                \Log::info('SinhVien Profile Update Request', [
                    'gioi_tinh_value' => $request->gioi_tinh,
                    'gioi_tinh_filled' => $request->filled('gioi_tinh'),
                    'all_request' => $request->all()
                ]);

                // Luôn cập nhật các trường này (nếu có giá trị)
                if ($request->filled('ngay_sinh')) $data['ngay_sinh'] = $request->ngay_sinh;
                if ($request->filled('gioi_tinh')) $data['gioi_tinh'] = $request->gioi_tinh;

                // Các trường optional
                if ($request->filled('so_nha_duong')) $data['so_nha_duong'] = $request->so_nha_duong;
                if ($request->filled('phuong_xa')) $data['phuong_xa'] = $request->phuong_xa;
                if ($request->filled('quan_huyen')) $data['quan_huyen'] = $request->quan_huyen;
                if ($request->filled('tinh_thanh')) $data['tinh_thanh'] = $request->tinh_thanh;
                if ($request->filled('can_cuoc_cong_dan')) $data['can_cuoc_cong_dan'] = $request->can_cuoc_cong_dan;
                if ($request->filled('ngay_cap_cccd')) $data['ngay_cap_cccd'] = $request->ngay_cap_cccd;
                if ($request->filled('noi_cap_cccd')) $data['noi_cap_cccd'] = $request->noi_cap_cccd;

                \Log::info('Data to update', $data);
                $updated = DB::table('sinh_vien')->where('user_id', $user->id)->update($data);
                \Log::info('Update result', ['rows_affected' => $updated]);
                break;

            case 'giang_vien':
                // Cập nhật các trường (cho phép null/empty)
                if ($request->has('ngay_sinh')) {
                    $data['ngay_sinh'] = $request->ngay_sinh ?: null;
                }
                if ($request->has('gioi_tinh')) {
                    $data['gioi_tinh'] = $request->gioi_tinh ?: null;
                }
                if ($request->has('dia_chi')) {
                    $data['dia_chi'] = $request->dia_chi ?: null;
                }
                if ($request->has('chuyen_mon')) {
                    $data['chuyen_mon'] = $request->chuyen_mon ?: null;
                }
                if ($request->has('ngay_vao_truong')) {
                    $data['ngay_vao_truong'] = $request->ngay_vao_truong ?: null;
                }

                DB::table('giang_vien')->where('user_id', $user->id)->update($data);
                break;

            case 'truong_phong_dt':
            case 'nhan_vien_dt':
                if ($request->filled('ngay_sinh')) $data['ngay_sinh'] = $request->ngay_sinh;
                if ($request->filled('gioi_tinh')) $data['gioi_tinh'] = $request->gioi_tinh;
                if ($request->filled('dia_chi')) $data['dia_chi'] = $request->dia_chi;
                if ($request->filled('ghi_chu')) $data['ghi_chu'] = $request->ghi_chu;

                DB::table('dao_tao')->where('user_id', $user->id)->update($data);
                break;

            case 'admin':
                if ($request->filled('ngay_sinh')) $data['ngay_sinh'] = $request->ngay_sinh;
                if ($request->filled('gioi_tinh')) $data['gioi_tinh'] = $request->gioi_tinh;
                if ($request->filled('dia_chi')) $data['dia_chi'] = $request->dia_chi;
                if ($request->filled('ghi_chu')) $data['ghi_chu'] = $request->ghi_chu;

                DB::table('admin')->where('user_id', $user->id)->update($data);
                break;
        }
    }
}
