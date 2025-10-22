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
     * Display a listing of admins
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
     * Show the form for creating a new admin
     */
    public function create()
    {
        // Get users that don't have admin role yet
        $users = User::whereDoesntHave('admin')->get();

        return view('admin.admin.create', compact('users'));
    }

    /**
     * Store a newly created admin
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
     * Update the specified admin
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
     * Remove the specified admin (soft delete)
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
     * Assign user to admin
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
     * Unassign user from admin
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
