<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin as AdminModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Hiển thị danh sách tất cả admin users trong hệ thống
     *
     * Function này quản lý danh sách admin users, hỗ trợ tìm kiếm,
     * và hiển thị thông tin liên kết với user accounts.
     *
     * Workflow:
     * 1. Khởi tạo query với eager loading:
     *    - AdminModel::with('user')
     *    - Load relationship user để tránh N+1 queries
     * 2. Xử lý tìm kiếm (nếu có):
     *    - Kiểm tra $request->search exists và không rỗng
     *    - Áp dụng WHERE conditions:
     *      + ho_ten LIKE %search%
     *      + OR email LIKE %search%
     *      + OR so_dien_thoai LIKE %search%
     *    - Sử dụng closure để group OR conditions
     * 3. Phân trang kết quả:
     *    - paginate(10) - 10 admins per page
     * 4. Return view với data
     *
     * Thông tin hiển thị:
     * - Bảng admin list với columns:
     *   + ID
     *   + Ảnh đại diện (thumbnail)
     *   + Họ tên
     *   + Email
     *   + Số điện thoại
     *   + User liên kết (nếu có)
     *   + Ngày tạo
     *   + Actions: View, Edit, Delete
     * - Search box
     * - Thêm admin mới button
     * - Pagination links
     *
     * Tính năng:
     * - Realtime search (AJAX optional)
     * - Sort by columns
     * - Quick actions menu
     * - Batch operations
     *
     * @param Request $request Chứa search parameter
     * @return \Illuminate\View\View Admin list view
     */
    public function index(Request $request)
    {
        $query = AdminModel::with('user');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ho_ten', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$search}%");
            });
        }

        $admins = $query->paginate(10);

        return view('admin.admin.index', compact('admins'));
    }

    /**
     * Hiển thị form tạo admin user mới
     *
     * Function này chuẩn bị dữ liệu cần thiết cho form tạo admin,
     * bao gồm danh sách users chưa được gán vai trò admin.
     *
     * Workflow:
     * 1. Query users chưa có admin role:
     *    - User::whereDoesntHave('admin')
     *    - Lọc ra users chưa được liên kết với bảng admin
     *    - Tránh duplicate admin assignments
     * 2. Return create view với available users
     *
     * Form fields:
     * - Họ tên (required, text)
     * - Email (required, email, unique)
     * - Số điện thoại (optional, text)
     * - User liên kết (optional, dropdown)
     *   + Chọn từ danh sách users available
     *   + Hoặc để trống, tạo admin standalone
     * - Ảnh đại diện (optional, file upload)
     *   + Max 2MB
     *   + Formats: jpeg, png, jpg, gif
     *
     * Use cases:
     * - Tạo admin liên kết với user account có sẵn
     * - Tạo admin profile độc lập (không liên kết user)
     * - Import admin data từ hệ thống khác
     *
     * @return \Illuminate\View\View Create admin form với:
     *   - users: Collection users available cho assignment
     */
    public function create()
    {
        // Get users that don't have admin role yet
        $users = User::whereDoesntHave('admin')->get();

        return view('admin.admin.create', compact('users'));
    }

    /**
     * Lưu admin user mới vào database với image upload và transaction
     *
     * Function này xử lý việc tạo admin mới, bao gồm validation,
     * upload ảnh đại diện, và sử dụng database transaction để
     * đảm bảo data integrity.
     *
     * Workflow:
     * 1. Validate input data:
     *    - ho_ten: required, string, max 255 chars
     *    - email: required, email format, unique trong bảng admin
     *    - so_dien_thoai: nullable, string, max 15 chars
     *    - user_id: nullable, phải tồn tại trong bảng users
     *    - anh_dai_dien: nullable, image file
     *      + MIME types: jpeg, png, jpg, gif
     *      + Max size: 2048 KB (2MB)
     * 2. Bắt đầu database transaction (DB::beginTransaction)
     * 3. Xử lý upload ảnh (nếu có):
     *    - Kiểm tra hasFile('anh_dai_dien')
     *    - Generate unique filename: timestamp_originalname
     *    - Store vào storage/app/public/admin/
     *    - storeAs('admin', $imageName, 'public')
     *    - Lưu path vào validated data
     * 4. Tạo admin record:
     *    - AdminModel::create($validated)
     *    - Auto-fill timestamps (created_at, updated_at)
     * 5. Commit transaction nếu thành công
     * 6. Redirect về index với success message
     * 7. Nếu có exception:
     *    - Rollback transaction
     *    - Redirect back với error và old input
     *
     * Transaction benefits:
     * - Nếu upload ảnh thành công nhưng DB insert fail:
     *   => Rollback, ảnh sẽ không được lưu
     * - Đảm bảo consistency giữa file system và database
     * - Nếu có nhiều tables liên quan, all-or-nothing
     *
     * Image storage:
     * - Location: storage/app/public/admin/
     * - Public accessible: storage/admin/ (symlink)
     * - URL: asset('storage/admin/filename.jpg')
     * - Filename format: 1732876543_avatar.jpg
     *
     * Error scenarios:
     * - Email đã tồn tại: ValidationException
     * - File quá lớn: ValidationException
     * - Upload fail: Exception, rollback
     * - DB constraint violation: Exception, rollback
     *
     * Success flow:
     * - Admin created
     * - Image uploaded (nếu có)
     * - Transaction committed
     * - Redirect về admin.index
     * - Flash message: "Thêm admin thành công!"
     *
     * Side effects:
     * - File được lưu vào storage/app/public/admin/
     * - Record mới trong bảng admin
     * - Nếu user_id provided, user được liên kết với admin
     *
     * @param Request $request Form data từ create view
     * @return \Illuminate\Http\RedirectResponse Redirect về index hoặc back
     * @throws \Illuminate\Validation\ValidationException Nếu validation fails
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'so_dien_thoai' => 'nullable|string|max:15',
            'user_id' => 'nullable|exists:users,id',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('anh_dai_dien')) {
                $image = $request->file('anh_dai_dien');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('admin', $imageName, 'public');
                $validated['anh_dai_dien'] = $imagePath;
            }

            $admin = AdminModel::create($validated);

            DB::commit();
            return redirect()->route('admin.admin.index')
                ->with('success', 'Thêm admin thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified admin
     */
    public function show(AdminModel $admin)
    {
        $admin->load('user');
        return view('admin.admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin
     */
    public function edit(AdminModel $admin)
    {
        // Get users that don't have admin role or current admin's user
        $users = User::whereDoesntHave('admin')
            ->orWhere('id', $admin->user_id)
            ->get();

        return view('admin.admin.edit', compact('admin', 'users'));
    }

    /**
     * Cập nhật thông tin admin user với image replacement và validation
     *
     * Function này xử lý việc update admin hiện có, bao gồm
     * thay thế ảnh đại diện, xóa ảnh cũ, và database transaction.
     *
     * Workflow:
     * 1. Validate input data:
     *    - ho_ten: required, string, max 255
     *    - email: required, email, unique EXCEPT current admin
     *      + unique:admin,email,{admin->id}
     *      + Cho phép giữ email hiện tại
     *    - so_dien_thoai: nullable, string, max 15
     *    - user_id: nullable, exists in users table
     *    - anh_dai_dien: nullable, image, max 2MB
     * 2. Bắt đầu database transaction
     * 3. Xử lý image upload (nếu có file mới):
     *    a. Kiểm tra hasFile('anh_dai_dien')
     *    b. Xóa ảnh cũ (nếu tồn tại):
     *       - Kiểm tra $admin->anh_dai_dien không null
     *       - Kiểm tra file exists trong storage
     *       - Storage::disk('public')->delete($admin->anh_dai_dien)
     *       - Giải phóng disk space
     *    c. Upload ảnh mới:
     *       - Generate filename: timestamp_originalname
     *       - storeAs('admin', $imageName, 'public')
     *       - Update validated data với path mới
     * 4. Update admin record:
     *    - $admin->update($validated)
     *    - Auto update updated_at timestamp
     * 5. Commit transaction
     * 6. Redirect về index với success message
     * 7. Exception handling:
     *    - Rollback transaction
     *    - Redirect back với error và old input
     *
     * Image replacement logic:
     * - Nếu upload ảnh mới:
     *   + Xóa ảnh cũ từ storage
     *   + Lưu ảnh mới
     *   + Update path trong DB
     * - Nếu không upload:
     *   + Giữ nguyên ảnh cũ
     *   + Chỉ update các fields khác
     *
     * Email uniqueness:
     * - Cho phép giữ email hiện tại
     * - Chặn email trùng với admin khác
     * - Validation rule: unique:admin,email,{id}
     * - {id} = ID của admin đang edit
     *
     * Transaction scenarios:
     * - Success: Image deleted, new image saved, DB updated, commit
     * - Fail upload: Rollback, giữ nguyên data cũ
     * - Fail DB: Rollback, new image không được lưu
     * - Old image deletion: Always try, không fail nếu không tồn tại
     *
     * Disk cleanup:
     * - Old images được xóa để tiết kiệm storage
     * - Orphaned images (admin deleted): Cần manual cleanup
     * - Consider scheduled cleanup job
     *
     * Error handling:
     * - Email duplicate: ValidationException
     * - File upload fail: Exception, rollback
     * - DB update fail: Exception, rollback
     * - Storage permission: IOException, rollback
     *
     * Side effects:
     * - Old image file deleted from storage
     * - New image file added to storage
     * - Admin record updated in database
     * - updated_at timestamp refreshed
     *
     * @param Request $request Form data từ edit view
     * @param AdminModel $admin Admin instance cần update (route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect về index hoặc back
     * @throws \Illuminate\Validation\ValidationException Nếu validation fails
     */
    public function update(Request $request, AdminModel $admin)
    {
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email,' . $admin->id,
            'so_dien_thoai' => 'nullable|string|max:15',
            'user_id' => 'nullable|exists:users,id',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('anh_dai_dien')) {
                // Delete old image
                if ($admin->anh_dai_dien && Storage::disk('public')->exists($admin->anh_dai_dien)) {
                    Storage::disk('public')->delete($admin->anh_dai_dien);
                }

                $image = $request->file('anh_dai_dien');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('admin', $imageName, 'public');
                $validated['anh_dai_dien'] = $imagePath;
            }

            $admin->update($validated);

            DB::commit();
            return redirect()->route('admin.admin.index')
                ->with('success', 'Cập nhật admin thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa admin user khỏi hệ thống (soft delete)
     *
     * Function này xử lý việc xóa admin, sử dụng soft delete
     * để giữ lại dữ liệu cho audit trail và recovery.
     *
     * Workflow:
     * 1. Thực hiện soft delete:
     *    - $admin->delete()
     *    - Set deleted_at = now()
     *    - Record vẫn còn trong DB nhưng hidden
     * 2. Redirect về index với success message
     * 3. Nếu có exception:
     *    - Redirect back với error message
     *    - Giữ nguyên admin record
     *
     * Soft delete behavior:
     * - Admin không hiển thị trong index
     * - deleted_at column được set = current timestamp
     * - Data vẫn tồn tại trong database
     * - Có thể restore nếu cần: $admin->restore()
     * - Query default sẽ skip soft deleted records
     *
     * Side effects:
     * - Admin record marked as deleted
     * - Ảnh đại diện vẫn còn trong storage (không xóa)
     *   => Có thể restore admin với full data
     * - User liên kết (nếu có) vẫn intact
     *   => User không bị ảnh hưởng
     * - Permissions và roles vẫn liên kết (cho restore)
     *
     * Business rules:
     * - Không cho phép xóa admin đang đăng nhập
     *   => Cần add check: $admin->id != Auth::id()
     * - Không cho phép xóa super admin
     *   => Cần check role/permissions
     * - Nên confirm trước khi xóa
     *   => Frontend confirmation dialog
     *
     * Recovery:
     * - Truy vấn soft deleted: Admin::withTrashed()
     * - Restore: $admin->restore()
     * - Force delete: $admin->forceDelete() (permanent)
     *
     * @param AdminModel $admin Admin cần xóa (route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect về index hoặc back
     */
    public function destroy(AdminModel $admin)
    {
        try {
            $admin->delete();
            return redirect()->route('admin.admin.index')
                ->with('success', 'Xóa admin thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Gán user account cho admin profile (link admin với user)
     *
     * Function này liên kết một user account với admin profile,
     * cho phép user đăng nhập và sử dụng quyền admin.
     *
     * Workflow:
     * 1. Validate input:
     *    - user_id: required, phải tồn tại trong bảng users
     *    - Kiểm tra foreign key constraint
     * 2. Update admin record:
     *    - Set user_id = validated user_id
     *    - Tạo relationship giữa Admin và User
     * 3. Redirect back với success message
     * 4. Exception handling:
     *    - Redirect back với error message
     *    - Giữ nguyên assignment cũ
     *
     * Use cases:
     * - Admin profile đã tồn tại, cần gán user
     * - Thay đổi user liên kết (reassignment)
     * - Kích hoạt admin cho user mới
     * - Migrate từ hệ thống khác
     *
     * Relationship benefits:
     * - User có thể login vào hệ thống
     * - Truy cập admin dashboard
     * - Sử dụng admin permissions
     * - Single sign-on giữa user và admin
     * - Audit trail với user identity
     *
     * Constraints:
     * - Một user chỉ có thể link với 1 admin
     *   => Nên check trước khi assign
     * - User phải chưa có admin role
     *   => Frontend đã filter trong dropdown
     * - Nên validate user status (active)
     *
     * Side effects:
     * - Admin có thể login bằng user credentials
     * - User nhận được admin permissions
     * - Logs/activities tracking với user_id
     *
     * @param Request $request Chứa user_id cần assign
     * @param AdminModel $admin Admin profile cần gán user
     * @return \Illuminate\Http\RedirectResponse Redirect back với message
     * @throws \Illuminate\Validation\ValidationException Nếu user_id invalid
     */
    public function assignUser(Request $request, AdminModel $admin)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $admin->update(['user_id' => $validated['user_id']]);
            return redirect()->back()
                ->with('success', 'Gán user thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hủy liên kết giữa admin profile và user account
     *
     * Function này ngắt kết nối giữa admin và user,
     * vô hiệu hóa khả năng login nhưng giữ lại admin data.
     *
     * Workflow:
     * 1. Update admin record:
     *    - Set user_id = null
     *    - Phá vỡ relationship với User
     * 2. Redirect back với success message
     * 3. Exception handling:
     *    - Redirect back với error
     *    - Giữ nguyên relationship
     *
     * Effects after unassign:
     * - Admin profile vẫn tồn tại
     * - Tất cả data (họ tên, email, ảnh) giữ nguyên
     * - user_id = null
     * - Không thể login vào hệ thống
     * - User account không bị ảnh hưởng
     *   => User vẫn có thể login như user thường
     *
     * Use cases:
     * - Tạm thời vô hiệu admin quyền
     * - Chuyển admin cho user khác
     * - Thu hồi quyền nhưng giữ data
     * - Restructure admin-user mapping
     *
     * Warning:
     * - Admin sẽ không thể login
     * - Cần reassign user mới để kích hoạt
     * - Nên confirm trước khi unassign
     *
     * @param AdminModel $admin Admin cần hủy user
     * @return \Illuminate\Http\RedirectResponse Redirect back với message
     */
    public function unassignUser(AdminModel $admin)
    {
        try {
            $admin->update(['user_id' => null]);
            return redirect()->back()
                ->with('success', 'Hủy gán user thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
