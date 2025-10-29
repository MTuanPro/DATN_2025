# LOGIC FLOW: XÁC THỰC & PHÂN QUYỀN

**Phase:** Phase 0  
**Actors:** Admin, Đào tạo, Giảng viên, Sinh viên  
**Độ ưu tiên:** ⭐⭐⭐⭐⭐ CỰC CAO

---

## 1. ĐĂNG NHẬP (LOGIN)

### 📊 Flowchart:

```
[User nhập email/password]
        ↓
[Validate input]
    ↓           ↓
[Invalid]   [Valid]
    ↓           ↓
[Return]   [Kiểm tra users table]
 Error          ↓
           [Tồn tại?]
        ↓               ↓
     [Không]          [Có]
        ↓               ↓
    [Return]      [Kiểm tra password]
     Error          ↓           ↓
              [Sai]         [Đúng]
                ↓             ↓
            [Return]    [Kiểm tra trang_thai]
             Error          ↓
                       [Active/Inactive?]
                    ↓                   ↓
                [Inactive]          [Active]
                    ↓                   ↓
                [Return]          [Lấy vai trò từ tai_khoan_vai_tro]
                 Error                  ↓
                              [Lấy quyền từ vai_tro_quyen]
                                        ↓
                              [Tạo session + token]
                                        ↓
                              [Ghi log nhat_ky_hoat_dong]
                                        ↓
                              [Redirect theo vai trò]
                                ↓       ↓       ↓       ↓
                            [Admin] [DT] [GV] [SV]
```

### 🔧 Chi tiết xử lý:

#### **Bước 1: Validate Input**

```php
// Request validation
$request->validate([
    'email' => 'required|email',
    'password' => 'required|min:6'
]);
```

#### **Bước 2: Kiểm tra User**

```php
// Query: users table
$user = User::where('email', $request->email)->first();

if (!$user) {
    return back()->withErrors(['email' => 'Email không tồn tại']);
}
```

#### **Bước 3: Kiểm tra Password**

```php
if (!Hash::check($request->password, $user->password)) {
    return back()->withErrors(['password' => 'Mật khẩu không đúng']);
}
```

#### **Bước 4: Kiểm tra Trạng thái**

```php
// Check: users.trang_thai
if ($user->trang_thai === 'inactive') {
    return back()->withErrors([
        'email' => 'Tài khoản đã bị khóa. Liên hệ admin.'
    ]);
}
```

#### **Bước 5: Lấy Vai trò & Quyền**

```php
// Query: tai_khoan_vai_tro -> vai_tro
$vaiTros = $user->vaiTros()->get();

if ($vaiTros->isEmpty()) {
    return back()->withErrors([
        'email' => 'Tài khoản chưa được gán vai trò'
    ]);
}

// Lấy vai trò có mức độ ưu tiên cao nhất
$vaiTroChinhId = $vaiTros->sortByDesc('muc_do_uu_tien')->first()->id;

// Query: vai_tro_quyen -> quyen
$quyens = VaiTroQuyen::where('vai_tro_id', $vaiTroChinhId)
    ->with('quyen')
    ->get()
    ->pluck('quyen.ma_quyen')
    ->toArray();
```

#### **Bước 6: Tạo Session**

```php
// Login Laravel Auth
Auth::login($user, $request->remember);

// Store session data
session([
    'vai_tro_id' => $vaiTroChinhId,
    'vai_tro_ten' => $vaiTros->first()->ten_vai_tro,
    'quyens' => $quyens,
]);
```

#### **Bước 7: Ghi Log**

```php
// Insert: nhat_ky_hoat_dong
NhatKyHoatDong::create([
    'user_id' => $user->id,
    'hanh_dong' => 'LOGIN',
    'bang_du_lieu' => 'users',
    'ban_ghi_id' => $user->id,
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'du_lieu_cu' => null,
    'du_lieu_moi' => null,
]);
```

#### **Bước 8: Redirect theo Vai trò**

```php
// Redirect based on role
switch ($vaiTroChinhId) {
    case 1: // Admin
        return redirect()->route('admin.dashboard');
    case 2: // Đào tạo
        return redirect()->route('daotao.dashboard');
    case 3: // Giảng viên
        return redirect()->route('giangvien.dashboard');
    case 4: // Sinh viên
        return redirect()->route('sinhvien.dashboard');
    default:
        return redirect()->route('home');
}
```

### 📋 Các bảng liên quan:

-   **users** (id, email, password, trang_thai)
-   **tai_khoan_vai_tro** (user_id, vai_tro_id)
-   **vai_tro** (id, ten_vai_tro, muc_do_uu_tien)
-   **vai_tro_quyen** (vai_tro_id, quyen_id)
-   **quyen** (id, ma_quyen, ten_quyen)
-   **nhat_ky_hoat_dong** (log activity)

---

## 2. ĐĂNG XUẤT (LOGOUT)

### 📊 Flowchart:

```
[User click Logout]
        ↓
[Ghi log nhat_ky_hoat_dong]
        ↓
[Xóa session]
        ↓
[Auth::logout()]
        ↓
[Redirect to login page]
```

### 🔧 Chi tiết xử lý:

```php
public function logout(Request $request) {
    // 1. Ghi log
    NhatKyHoatDong::create([
        'user_id' => Auth::id(),
        'hanh_dong' => 'LOGOUT',
        'bang_du_lieu' => 'users',
        'ban_ghi_id' => Auth::id(),
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    // 2. Logout
    Auth::logout();

    // 3. Invalidate session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // 4. Redirect
    return redirect()->route('login')
        ->with('success', 'Đăng xuất thành công');
}
```

---

## 3. QUÊN MẬT KHẨU (FORGOT PASSWORD)

### 📊 Flowchart:

```
[User nhập email]
        ↓
[Validate email]
        ↓
[Kiểm tra email tồn tại?]
    ↓               ↓
  [Không]         [Có]
    ↓               ↓
 [Return]    [Tạo token reset]
  Error            ↓
            [Lưu vào password_reset_tokens]
                    ↓
            [Gửi email với link reset]
                    ↓
            [Return success message]
```

### 🔧 Chi tiết xử lý:

#### **Bước 1: Validate & Check Email**

```php
$request->validate(['email' => 'required|email']);

$user = User::where('email', $request->email)->first();

if (!$user) {
    return back()->withErrors(['email' => 'Email không tồn tại']);
}
```

#### **Bước 2: Tạo Token**

```php
// Generate random token
$token = Str::random(64);

// Insert: password_reset_tokens
DB::table('password_reset_tokens')->updateOrInsert(
    ['email' => $request->email],
    [
        'token' => Hash::make($token),
        'created_at' => now()
    ]
);
```

#### **Bước 3: Gửi Email**

```php
// Send email with reset link
Mail::to($user->email)->send(new ResetPasswordMail($user, $token));
```

#### **Bước 4: Response**

```php
return back()->with('success',
    'Link đặt lại mật khẩu đã được gửi đến email của bạn'
);
```

### 📋 Các bảng liên quan:

-   **users**
-   **password_reset_tokens** (email, token, created_at)

---

## 4. RESET MẬT KHẨU (RESET PASSWORD)

### 📊 Flowchart:

```
[User click link từ email với token]
        ↓
[Validate token]
    ↓           ↓
[Invalid]   [Valid]
    ↓           ↓
[Return]   [Hiển thị form nhập password mới]
 Error          ↓
           [User nhập password mới]
                ↓
           [Validate password]
                ↓
           [Update users.password]
                ↓
           [Xóa token khỏi password_reset_tokens]
                ↓
           [Ghi log]
                ↓
           [Redirect to login]
```

### 🔧 Chi tiết xử lý:

```php
public function resetPassword(Request $request) {
    // 1. Validate
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
        'token' => 'required'
    ]);

    // 2. Check token
    $resetRecord = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
        return back()->withErrors(['token' => 'Token không hợp lệ']);
    }

    // 3. Check token expiry (15 minutes)
    if (now()->diffInMinutes($resetRecord->created_at) > 15) {
        return back()->withErrors(['token' => 'Token đã hết hạn']);
    }

    // 4. Update password
    User::where('email', $request->email)->update([
        'password' => Hash::make($request->password)
    ]);

    // 5. Delete token
    DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->delete();

    // 6. Log
    NhatKyHoatDong::create([
        'user_id' => User::where('email', $request->email)->first()->id,
        'hanh_dong' => 'RESET_PASSWORD',
        'bang_du_lieu' => 'users',
        'ip_address' => $request->ip(),
    ]);

    // 7. Redirect
    return redirect()->route('login')
        ->with('success', 'Mật khẩu đã được đặt lại thành công');
}
```

---

## 5. KIỂM TRA QUYỀN (AUTHORIZATION)

### 📊 Flowchart:

```
[User truy cập route cần quyền]
        ↓
[Middleware CheckPermission]
        ↓
[Lấy vai trò của user từ session]
        ↓
[Lấy danh sách quyền của vai trò]
        ↓
[Kiểm tra quyền cần thiết có trong danh sách?]
    ↓                   ↓
  [Không]             [Có]
    ↓                   ↓
[Return 403]      [Cho phép truy cập]
 Forbidden
```

### 🔧 Middleware CheckPermission:

```php
public function handle($request, Closure $next, $permission) {
    // 1. Check authenticated
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // 2. Get user permissions from session
    $userPermissions = session('quyens', []);

    // 3. Check permission
    if (!in_array($permission, $userPermissions)) {
        abort(403, 'Bạn không có quyền truy cập chức năng này');
    }

    // 4. Allow access
    return $next($request);
}
```

### 🔧 Sử dụng trong Route:

```php
// routes/web.php
Route::middleware(['auth', 'permission:quan_ly_sinh_vien'])
    ->group(function() {
        Route::get('/admin/sinh-vien', [SinhVienController::class, 'index']);
    });
```

---

## 6. ĐỔI MẬT KHẨU (CHANGE PASSWORD)

### 📊 Flowchart:

```
[User vào form đổi mật khẩu]
        ↓
[Nhập: mật khẩu cũ, mật khẩu mới, xác nhận]
        ↓
[Validate input]
        ↓
[Kiểm tra mật khẩu cũ đúng?]
    ↓               ↓
  [Sai]          [Đúng]
    ↓               ↓
[Return]      [Update password]
 Error              ↓
              [Ghi log]
                    ↓
              [Logout user]
                    ↓
              [Redirect to login với thông báo]
```

### 🔧 Chi tiết xử lý:

```php
public function changePassword(Request $request) {
    // 1. Validate
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    // 2. Check current password
    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return back()->withErrors([
            'current_password' => 'Mật khẩu hiện tại không đúng'
        ]);
    }

    // 3. Update password
    Auth::user()->update([
        'password' => Hash::make($request->new_password)
    ]);

    // 4. Log
    NhatKyHoatDong::create([
        'user_id' => Auth::id(),
        'hanh_dong' => 'CHANGE_PASSWORD',
        'bang_du_lieu' => 'users',
        'ban_ghi_id' => Auth::id(),
        'ip_address' => $request->ip(),
    ]);

    // 5. Logout và yêu cầu đăng nhập lại
    Auth::logout();

    return redirect()->route('login')
        ->with('success', 'Mật khẩu đã được thay đổi. Vui lòng đăng nhập lại.');
}
```

---

## 📊 BẢNG MAPPING: VaiTro → Route → Permission

| Vai trò    | Dashboard Route        | Quyền mặc định                                                      |
| ---------- | ---------------------- | ------------------------------------------------------------------- |
| Admin      | `/admin/dashboard`     | `*` (all permissions)                                               |
| Đào tạo    | `/daotao/dashboard`    | `quan_ly_sinh_vien`, `quan_ly_lop_hoc_phan`, `phan_cong_giang_vien` |
| Giảng viên | `/giangvien/dashboard` | `xem_lop_day`, `diem_danh`, `nhap_diem`                             |
| Sinh viên  | `/sinhvien/dashboard`  | `dang_ky_mon_hoc`, `xem_diem`, `xem_lich_hoc`                       |

---

## 🔒 BẢO MẬT & LƯU Ý

### Security Best Practices:

1. ✅ Hash password với bcrypt (cost = 10)
2. ✅ Validate tất cả input
3. ✅ CSRF protection (Laravel mặc định)
4. ✅ Rate limiting cho login (5 attempts / 1 minute)
5. ✅ Token reset password hết hạn sau 15 phút
6. ✅ Logout user sau khi đổi password
7. ✅ Ghi log tất cả hành động quan trọng

### Rate Limiting Login:

```php
// LoginController.php
protected $maxAttempts = 5; // 5 lần thử
protected $decayMinutes = 1; // trong 1 phút

public function login(Request $request) {
    // Check rate limit
    if ($this->hasTooManyLoginAttempts($request)) {
        return back()->withErrors([
            'email' => 'Quá nhiều lần thử đăng nhập. Vui lòng thử lại sau 1 phút.'
        ]);
    }

    // ... login logic ...

    // Clear attempts if successful
    $this->clearLoginAttempts($request);
}
```

---

**Ngày tạo:** 27/10/2025  
**Phase:** Phase 0 - Authentication & Authorization  
**Trạng thái:** ✅ Đã hoàn thành (theo HOÀN THÀNH section)
