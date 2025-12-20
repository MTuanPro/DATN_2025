<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\Admin;
use App\Models\DaoTao;
use App\Mail\VerifyEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản có phân trang, tìm kiếm và lọc
     *
     * Hỗ trợ các tính năng:
     * - Tìm kiếm theo tên hoặc email (param: search)
     * - Lọc theo trạng thái (hoạt động, khóa, ngừng hoạt động) (param: status)
     * - Lọc theo vai trò (param: role)
     * - Phân trang 15 tài khoản/trang
     *
     * @param Request $request Chứa các tham số tìm kiếm/lọc (search, status, role)
     * @return \Illuminate\View\View View danh sách tài khoản với thông tin vai trò
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
     * Hiển thị form tạo tài khoản người dùng mới
     *
     * Load danh sách tất cả vai trò được sắp xếp theo mức độ ưu tiên
     * để hiển thị trong dropdown select vai trò.
     *
     * @return \Illuminate\View\View Form tạo tài khoản với danh sách vai trò
     */
    public function create()
    {
        $vaiTros = VaiTro::orderBy('muc_do_uu_tien', 'desc')->get();
        return view('admin.users.create', compact('vaiTros'));
    }

    /**
     * Lưu tài khoản người dùng mới vào database với các tự động hóa
     *
     * Quy trình tạo tài khoản:
     * 1. Validate dữ liệu đầu vào (name, email, password, trạng thái, vai trò)
     * 2. Tạo bản ghi User với mật khẩu đã hash
     * 3. Gán vai trò cho user (attach vào bảng pivot)
     * 4. Tự động tạo profile Admin nếu gán vai trò 'admin' (mã tự động: AD + năm + số thứ tự)
     * 5. Tự động tạo profile DaoTao nếu gán vai trò 'đào tạo' (mã: DT + năm + số thứ tự)
     * 6. Tạo token xác thực email và gửi email xác thực
     *
     * @param Request $request Chứa name, email, password, password_confirmation, trang_thai, vai_tro
     * @return \Illuminate\Http\RedirectResponse Redirect về index với thông báo thành công/lỗi
     * @throws \Exception Khi có lỗi trong quá trình tạo tài khoản
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'trang_thai' => ['required', 'in:hoat_dong,khoa,ngung_hoat_dong'],
            'vai_tro' => ['required', 'exists:vai_tro,id'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
            'vai_tro.required' => 'Vui lòng chọn vai trò',
            'vai_tro.exists' => 'Vai trò không hợp lệ',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'trang_thai' => $validated['trang_thai'],
            ]);

            // Gán vai trò
            $vaiTroId = $validated['vai_tro'];
            $user->vaiTro()->attach($vaiTroId);

            // Tự động tạo Admin profile nếu gán vai trò admin
            $adminRole = VaiTro::where('ma_vai_tro', 'admin')->first();
            if ($adminRole && $adminRole->id == $vaiTroId) {
                // Kiểm tra xem đã có profile chưa
                $existingAdmin = Admin::where('user_id', $user->id)->first();

                if (!$existingAdmin) {
                    // Tạo mã admin tự động: AD + năm + số thứ tự
                    $year = date('Y');
                    $lastAdmin = Admin::whereYear('created_at', $year)
                        ->orderBy('id', 'desc')
                        ->first();

                    $sequence = $lastAdmin ? (int)substr($lastAdmin->ma_admin, -4) + 1 : 1;
                    $maAdmin = 'AD' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                    Admin::create([
                        'user_id' => $user->id,
                        'ma_admin' => $maAdmin,
                        'ho_ten' => $user->name,
                        'email' => $user->email,
                    ]);
                }
            }

            // Tự động tạo DaoTao profile nếu gán vai trò truong_phong_dt hoặc nhan_vien_dt
            $daoTaoRoles = VaiTro::whereIn('ma_vai_tro', ['truong_phong_dt', 'nhan_vien_dt'])->pluck('id')->toArray();
            if (in_array($vaiTroId, $daoTaoRoles)) {
                // Kiểm tra xem đã có profile chưa
                $existingDaoTao = DaoTao::where('user_id', $user->id)->first();

                if (!$existingDaoTao) {
                    // Tạo mã đào tạo tự động: DT + năm + số thứ tự
                    $year = date('Y');
                    $lastDaoTao = DaoTao::whereYear('created_at', $year)
                        ->orderBy('id', 'desc')
                        ->first();

                    $sequence = $lastDaoTao ? (int)substr($lastDaoTao->ma_dao_tao, -4) + 1 : 1;
                    $maDaoTao = 'DT' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                    DaoTao::create([
                        'user_id' => $user->id,
                        'ma_dao_tao' => $maDaoTao,
                        'ho_ten' => $user->name,
                        'email' => $user->email,
                    ]);
                }
            }

            // Tạo token xác thực email
            $token = Str::random(64);

            // Xóa token cũ nếu có
            DB::table('email_verification_tokens')
                ->where('email', $user->email)
                ->delete();

            // Tạo token mới
            DB::table('email_verification_tokens')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Tạo URL xác thực
            $verificationUrl = url('/email/verify/' . $token . '?email=' . urlencode($user->email));

            // Gửi email xác thực
            Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Tạo tài khoản thành công! Email xác thực đã được gửi đến ' . $user->email);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form sửa thông tin tài khoản người dùng
     *
     * Load thông tin user hiện tại, danh sách vai trò, và các vai trò đang được gán.
     *
     * @param User $user Tài khoản cần chỉnh sửa (route model binding)
     * @return \Illuminate\View\View Form chỉnh sửa với dữ liệu user và vai trò
     */
    public function edit(User $user)
    {
        $vaiTros = VaiTro::orderBy('muc_do_uu_tien', 'desc')->get();
        $userVaiTroIds = $user->vaiTro->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'vaiTros', 'userVaiTroIds'));
    }

    /**
     * Cập nhật thông tin tài khoản người dùng và xử lý thay đổi vai trò
     *
     * Quy trình cập nhật:
     * 1. Validate dữ liệu (name, email unique trừ user hiện tại, trạng thái, vai trò)
     * 2. Cập nhật thông tin cơ bản (name, email, trang_thai)
     * 3. Nếu email thay đổi: reset email_verified_at, tạo token mới, gửi email xác thực
     * 4. Sync vai trò mới (thêm/xóa trong bảng pivot)
     * 5. Tự động tạo/xóa profile Admin dựa trên vai trò 'admin'
     * 6. Tự động tạo/xóa profile DaoTao dựa trên vai trò 'đào tạo'
     *
     * @param Request $request Chứa name, email, trang_thai, vai_tro
     * @param User $user Tài khoản cần cập nhật (route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect về index với thông báo
     * @throws \Exception Khi có lỗi trong quá trình cập nhật
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'trang_thai' => ['required', 'in:hoat_dong,khoa,ngung_hoat_dong'],
            'vai_tro' => ['nullable', 'exists:vai_tro,id'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
        ]);

        DB::beginTransaction();
        try {
            // Kiểm tra xem email có thay đổi không (trước khi update)
            $oldEmail = $user->getOriginal('email');
            $emailChanged = $oldEmail !== $validated['email'];

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'trang_thai' => $validated['trang_thai'],
            ]);

            // Nếu email thay đổi → reset email_verified_at và gửi email xác thực mới
            if ($emailChanged) {
                $user->email_verified_at = null;
                $user->save();

                // Tạo token xác thực email
                $token = Str::random(64);

                // Xóa token cũ nếu có
                DB::table('email_verification_tokens')
                    ->where('email', $user->email)
                    ->delete();

                // Tạo token mới
                DB::table('email_verification_tokens')->insert([
                    'email' => $user->email,
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]);

                // Tạo URL xác thực
                $verificationUrl = url('/email/verify/' . $token . '?email=' . urlencode($user->email));

                // Gửi email xác thực
                Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));
            }

            // Cập nhật vai trò
            if (isset($validated['vai_tro'])) {
                $user->vaiTro()->sync([$validated['vai_tro']]);

                // Tự động tạo Admin profile nếu gán vai trò admin
                $adminRole = VaiTro::where('ma_vai_tro', 'admin')->first();
                if ($adminRole && $adminRole->id == $validated['vai_tro']) {
                    // Kiểm tra xem đã có profile chưa
                    $existingAdmin = Admin::where('user_id', $user->id)->first();

                    if (!$existingAdmin) {
                        // Tạo mã admin tự động: AD + năm + số thứ tự
                        $year = date('Y');
                        $lastAdmin = Admin::whereYear('created_at', $year)
                            ->orderBy('id', 'desc')
                            ->first();

                        $sequence = $lastAdmin ? (int)substr($lastAdmin->ma_admin, -4) + 1 : 1;
                        $maAdmin = 'AD' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                        Admin::create([
                            'user_id' => $user->id,
                            'ma_admin' => $maAdmin,
                            'ho_ten' => $user->name,
                            'email' => $user->email,
                        ]);
                    }
                } else {
                    // Xóa Admin profile nếu bỏ vai trò admin
                    Admin::where('user_id', $user->id)->delete();
                }

                // Tự động tạo DaoTao profile nếu gán vai trò truong_phong_dt hoặc nhan_vien_dt
                $daoTaoRoles = VaiTro::whereIn('ma_vai_tro', ['truong_phong_dt', 'nhan_vien_dt'])->pluck('id')->toArray();
                if (in_array($validated['vai_tro'], $daoTaoRoles)) {
                    // Kiểm tra xem đã có profile chưa
                    $existingDaoTao = DaoTao::where('user_id', $user->id)->first();

                    if (!$existingDaoTao) {
                        // Tạo mã đào tạo tự động: DT + năm + số thứ tự
                        $year = date('Y');
                        $lastDaoTao = DaoTao::whereYear('created_at', $year)
                            ->orderBy('id', 'desc')
                            ->first();

                        $sequence = $lastDaoTao ? (int)substr($lastDaoTao->ma_dao_tao, -4) + 1 : 1;
                        $maDaoTao = 'DT' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                        DaoTao::create([
                            'user_id' => $user->id,
                            'ma_dao_tao' => $maDaoTao,
                            'ho_ten' => $user->name,
                            'email' => $user->email,
                        ]);
                    }
                } else {
                    // Xóa DaoTao profile nếu bỏ vai trò đào tạo
                    DaoTao::where('user_id', $user->id)->delete();
                }
            } else {
                $user->vaiTro()->detach();
                // Xóa cả Admin và DaoTao profile nếu bỏ hết vai trò
                Admin::where('user_id', $user->id)->delete();
                DaoTao::where('user_id', $user->id)->delete();
            }

            DB::commit();

            $message = 'Cập nhật thông tin thành công!';
            if ($emailChanged) {
                $message .= ' Email xác thực đã được gửi đến ' . $user->email;
            }

            return redirect()->route('admin.users.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa tài khoản người dùng và tất cả dữ liệu liên quan
     *
     * Quy trình xóa:
     * 1. Kiểm tra không cho phép xóa chính mình
     * 2. Xóa các profile liên quan (Admin, DaoTao, SinhVien, GiangVien) - force delete
     * 3. Xóa quan hệ vai trò (detach từ bảng pivot)
     * 4. Xóa dữ liệu phụ (nhật ký, thông báo, token, session)
     * 5. Xóa bản ghi User (các bảng khác có onDelete cascade sẽ tự xóa)
     *
     * @param User $user Tài khoản cần xóa (route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect về index với thông báo
     * @throws \Exception Khi có lỗi trong quá trình xóa
     */
    public function destroy(User $user)
    {
        // Không cho phép xóa chính mình
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính bạn!');
        }

        DB::beginTransaction();
        try {
            // Xóa các bảng liên quan trước
            // Xóa Admin profile nếu có (force delete để xóa hoàn toàn)
            if ($user->admin) {
                $user->admin->forceDelete();
            }

            // Xóa DaoTao profile nếu có (force delete để xóa hoàn toàn)
            if ($user->daoTao) {
                $user->daoTao->forceDelete();
            }

            // Xóa SinhVien profile nếu có (force delete để xóa hoàn toàn)
            if ($user->sinhVien) {
                $user->sinhVien->forceDelete();
            }

            // Xóa GiangVien profile nếu có (force delete để xóa hoàn toàn)
            if ($user->giangVien) {
                $user->giangVien->forceDelete();
            }

            // Xóa các vai trò (xóa trong bảng pivot)
            $user->vaiTro()->detach();

            // Xóa các bảng liên quan khác có foreign key đến user
            // Xóa nhật ký hoạt động
            DB::table('nhat_ky_hoat_dong')->where('user_id', $user->id)->delete();
            
            // Xóa người nhận thông báo
            DB::table('nguoi_nhan_thong_bao')->where('nguoi_nhan_id', $user->id)->delete();
            
            // Xóa token xác thực email
            DB::table('email_verification_tokens')->where('email', $user->email)->delete();
            
            // Xóa token reset password
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            
            // Xóa session
            DB::table('sessions')->where('user_id', $user->id)->delete();

            // Xóa user (các bảng khác có foreign key với onDelete('cascade') sẽ tự động xóa)
            $user->delete();

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Xóa tài khoản thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra khi xóa tài khoản: ' . $e->getMessage());
        }
    }

    /**
     * Khóa/Mở khóa/Kích hoạt lại tài khoản qua AJAX
     *
     * Logic toggle trạng thái:
     * - hoat_dong → khoa (khóa tài khoản)
     * - khoa → hoat_dong (mở khóa tài khoản)
     * - ngung_hoat_dong → hoat_dong (kích hoạt lại tài khoản)
     *
     * Không cho phép khóa chính mình.
     *
     * @param User $user Tài khoản cần thay đổi trạng thái (route model binding)
     * @return \Illuminate\Http\JsonResponse JSON {success, message, status}
     * @throws \Exception Khi có lỗi trong quá trình cập nhật
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
            // Logic toggle: 
            // - hoat_dong → khoa (khóa tài khoản)
            // - khoa → hoat_dong (mở khóa tài khoản)
            // - ngung_hoat_dong → hoat_dong (kích hoạt lại tài khoản)
            if ($user->trang_thai === 'hoat_dong') {
                $newStatus = 'khoa';
                $message = 'Đã khóa tài khoản!';
            } else {
                // Nếu là khoa hoặc ngung_hoat_dong thì chuyển về hoat_dong
                $newStatus = 'hoat_dong';
                $message = $user->trang_thai === 'khoa' ? 'Đã mở khóa tài khoản!' : 'Đã kích hoạt lại tài khoản!';
            }
            
            $user->update(['trang_thai' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => $message,
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
     * Gửi email chứa link đặt lại mật khẩu cho người dùng
     *
     * Quy trình:
     * 1. Tạo token ngẫu nhiên 64 ký tự
     * 2. Xóa các token reset cũ (nếu có)
     * 3. Lưu token mới vào password_reset_tokens (hash)
     * 4. Tạo URL reset password với token và email
     * 5. Gửi email chứa link reset cho người dùng
     *
     * @param User $user Tài khoản cần reset mật khẩu (route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect về trang trước với thông báo
     * @throws \Exception Khi có lỗi gửi email
     */
    public function resetPassword(User $user)
    {
        try {
            // Tạo token
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

            // Tạo URL reset (không dùng route helper)
            $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($user->email));

            // Gửi email
            Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($user, $resetUrl));

            return back()->with('success', '✅ Đã gửi email chứa link đặt lại mật khẩu đến: ' . $user->email);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form nhập mật khẩu mới khi đặt lại mật khẩu
     *
     * Nhận token và email từ URL query params.
     *
     * @param Request $request Chứa token và email trong query string
     * @return \Illuminate\View\View Form đặt lại mật khẩu
     */
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    /**
     * Xử lý đặt lại mật khẩu mới với các kiểm tra bảo mật
     *
     * Quy trình:
     * 1. Validate dữ liệu (token, email, password với yêu cầu mạnh: chữ hoa/thường, số, ký tự đặc biệt)
     * 2. Kiểm tra token tồn tại trong password_reset_tokens
     * 3. Kiểm tra token khớp với giá trị đã hash
     * 4. Kiểm tra token chưa hết hạn (60 phút)
     * 5. Cập nhật mật khẩu mới (hash)
     * 6. Xóa token đã sử dụng
     *
     * @param Request $request Chứa token, email, password, password_confirmation
     * @return \Illuminate\Http\RedirectResponse Redirect đến login với thông báo
     */
    public function processReset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'password.required' => 'Mật khẩu không được để trống',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự',
            'password.letters' => 'Mật khẩu phải chứa chữ cái',
            'password.mixed' => 'Mật khẩu phải chứa cả chữ hoa và chữ thường',
            'password.numbers' => 'Mật khẩu phải chứa số',
            'password.symbols' => 'Mật khẩu phải chứa ký tự đặc biệt',
        ]);

        // Kiểm tra token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Token không hợp lệ!']);
        }

        // Kiểm tra token có đúng không
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Token không hợp lệ!']);
        }

        // Kiểm tra token đã hết hạn chưa (60 phút)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            return back()->withErrors(['email' => 'Link đặt lại mật khẩu đã hết hạn!']);
        }

        // Cập nhật mật khẩu
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Xóa token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');
    }

    /**
     * Xem lịch sử đăng nhập của tài khoản người dùng
     *
     * Tính năng chưa hoàn thiện - cần tạo bảng login_history để lưu lịch sử.
     *
     * @param User $user Tài khoản cần xem lịch sử (route model binding)
     * @return \Illuminate\View\View Trang lịch sử đăng nhập
     * @todo Tạo bảng login_history để lưu trữ thông tin đăng nhập
     */
    public function loginHistory(User $user)
    {
        // TODO: Cần tạo bảng login_history
        return view('admin.users.login-history', compact('user'));
    }

    /**
     * Buộc đăng xuất tài khoản người dùng (xóa session và remember token)
     *
     * Đặt remember_token = null để force logout.
     * Không cho phép force logout chính mình.
     *
     * @param User $user Tài khoản cần force logout (route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect về trang trước với thông báo
     * @throws \Exception Khi có lỗi trong quá trình cập nhật
     */
    public function forceLogout(User $user)
    {
        // Không cho phép force logout chính mình
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Không thể force logout tài khoản của chính bạn!');
        }

        try {
            // Xóa remember token
            $user->update([
                'remember_token' => null
            ]);

            return back()->with('success', 'Đã force logout tài khoản này!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang xác thực email từ link trong email
     *
     * Nhận token từ URL param và email từ query string.
     * Kiểm tra email có tồn tại trong query.
     *
     * @param string $token Token xác thực email từ URL
     * @param Request $request Chứa email trong query string
     * @return \Illuminate\View\View Form xác thực email
     * @return \Illuminate\Http\RedirectResponse Redirect đến login nếu thiếu email
     */
    public function showVerifyForm($token, Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Link xác thực không hợp lệ!');
        }

        return view('auth.verify-email-form', compact('token', 'email'));
    }

    /**
     * Xử lý xác thực email với kiểm tra token và hạn sử dụng
     *
     * Quy trình:
     * 1. Validate token và email
     * 2. Kiểm tra token tồn tại trong email_verification_tokens
     * 3. Kiểm tra token chưa hết hạn (60 phút)
     * 4. Kiểm tra token khớp với giá trị đã hash
     * 5. Cập nhật email_verified_at = now()
     * 6. Xóa token đã sử dụng
     *
     * @param Request $request Chứa token và email
     * @return \Illuminate\Http\RedirectResponse Redirect đến login với thông báo thành công/lỗi
     */
    public function processVerify(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
        ]);

        // Tìm token trong database
        $tokenData = DB::table('email_verification_tokens')
            ->where('email', $request->email)
            ->first();

        // Kiểm tra token tồn tại
        if (!$tokenData) {
            return back()->withErrors([
                'email' => 'Token xác thực không tồn tại hoặc đã hết hạn!',
            ])->withInput();
        }

        // Kiểm tra token đã quá 60 phút chưa
        if (now()->diffInMinutes($tokenData->created_at) > 60) {
            DB::table('email_verification_tokens')->where('email', $request->email)->delete();
            return back()->withErrors([
                'email' => 'Link xác thực đã hết hạn! Vui lòng liên hệ admin để gửi lại.',
            ])->withInput();
        }

        // Kiểm tra token có khớp không
        if (!Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors([
                'email' => 'Token xác thực không hợp lệ!',
            ])->withInput();
        }

        // Cập nhật email_verified_at cho user
        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        // Xóa token đã sử dụng
        DB::table('email_verification_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Email đã được xác thực thành công! Bạn có thể đăng nhập ngay.');
    }
}
