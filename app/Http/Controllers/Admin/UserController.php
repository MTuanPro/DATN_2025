<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản (có phân trang)
     */
    public function index(Request $request)
    {
        $query = User::with('vaiTro');

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        // Lọc theo vai trò
        if ($request->filled('role')) {
            $query->whereHas('vaiTro', function ($q) use ($request) {
                $q->where('ma_vai_tro', $request->role);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $vaiTros = VaiTro::all();

        return view('admin.users.index', compact('users', 'vaiTros'));
    }

    /**
     * Hiển thị form tạo tài khoản mới
     */
    public function create()
    {
        $vaiTros = VaiTro::orderBy('muc_do_uu_tien', 'desc')->get();
        return view('admin.users.create', compact('vaiTros'));
    }

    /**
     * Lưu tài khoản mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'trang_thai' => ['required', 'in:hoat_dong,khoa,ngung_hoat_dong'],
            'vai_tro' => ['nullable', 'array'],
            'vai_tro.*' => ['exists:vai_tro,id'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'trang_thai' => $validated['trang_thai'],
            ]);

            // Gán vai trò nếu có
            if (!empty($validated['vai_tro'])) {
                $user->vaiTro()->attach($validated['vai_tro']);
            }

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Tạo tài khoản thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form sửa thông tin tài khoản
     */
    public function edit(User $user)
    {
        $vaiTros = VaiTro::orderBy('muc_do_uu_tien', 'desc')->get();
        $userVaiTroIds = $user->vaiTro->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'vaiTros', 'userVaiTroIds'));
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'trang_thai' => ['required', 'in:hoat_dong,khoa,ngung_hoat_dong'],
            'vai_tro' => ['nullable', 'array'],
            'vai_tro.*' => ['exists:vai_tro,id'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'trang_thai' => $validated['trang_thai'],
            ]);

            // Cập nhật vai trò
            if (isset($validated['vai_tro'])) {
                $user->vaiTro()->sync($validated['vai_tro']);
            } else {
                $user->vaiTro()->detach();
            }

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa tài khoản
     */
    public function destroy(User $user)
    {
        // Không cho phép xóa chính mình
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính bạn!');
        }

        try {
            $user->vaiTro()->detach(); // Xóa các vai trò
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Xóa tài khoản thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Khóa/Mở khóa tài khoản
     */
    public function toggleStatus(User $user)
    {
        // Không cho phép khóa chính mình
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể khóa tài khoản của chính bạn!'
            ], 403);
        }

        try {
            $newStatus = $user->trang_thai === 'hoat_dong' ? 'khoa' : 'hoat_dong';
            $user->update(['trang_thai' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => $newStatus === 'khoa' ? 'Đã khóa tài khoản!' : 'Đã mở khóa tài khoản!',
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đặt lại mật khẩu
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'new_password.required' => 'Vui lòng nhập mật khẩu mới',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'new_password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
        ]);

        try {
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            return back()->with('success', 'Đặt lại mật khẩu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xác thực email
     */
    public function verifyEmail(User $user)
    {
        try {
            $user->update([
                'email_verified_at' => now()
            ]);

            return back()->with('success', 'Xác thực email thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xem lịch sử đăng nhập
     */
    public function loginHistory(User $user)
    {
        // TODO: Cần tạo bảng login_history để lưu lịch sử đăng nhập
        // Hiện tại chỉ hiển thị lần đăng nhập cuối
        return view('admin.users.login-history', compact('user'));
    }

    /**
     * Force logout tài khoản (xóa session)
     */
    public function forceLogout(User $user)
    {
        // Không cho phép force logout chính mình
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Không thể force logout tài khoản của chính bạn!');
        }

        try {
            // Xóa remember token để bắt buộc đăng nhập lại
            $user->update([
                'remember_token' => null
            ]);

            // TODO: Xóa tất cả sessions của user này từ database
            // Cần config session driver = database

            return back()->with('success', 'Đã force logout tài khoản này!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
