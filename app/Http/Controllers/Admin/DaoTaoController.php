<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DaoTao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DaoTaoController extends Controller
{
    /**
     * Display a listing of dao tao staff
     */
    public function index(Request $request)
    {
        $query = DaoTao::with('user');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ho_ten', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$search}%");
            });
        }

        $daoTaos = $query->paginate(10);

        return view('admin.dao-tao.index', compact('daoTaos'));
    }

    /**
     * Show the form for creating a new dao tao staff
     */
    public function create()
    {
        // Get users that don't have dao_tao role yet
        $users = User::whereDoesntHave('daoTao')->get();

        return view('admin.dao-tao.create', compact('users'));
    }

    /**
     * Store a newly created dao tao staff
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:dao_tao,email',
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
                $imagePath = $image->storeAs('dao_tao', $imageName, 'public');
                $validated['anh_dai_dien'] = $imagePath;
            }

            $daoTao = DaoTao::create($validated);

            DB::commit();
            return redirect()->route('admin.dao-tao.index')
                ->with('success', 'Thêm nhân viên đào tạo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified dao tao staff
     */
    public function show(DaoTao $daoTao)
    {
        $daoTao->load('user');
        return view('admin.dao-tao.show', compact('daoTao'));
    }

    /**
     * Show the form for editing the specified dao tao staff
     */
    public function edit(DaoTao $daoTao)
    {
        // Get users that don't have dao_tao role or current dao_tao's user
        $users = User::whereDoesntHave('daoTao')
            ->orWhere('id', $daoTao->user_id)
            ->get();

        return view('admin.dao-tao.edit', compact('daoTao', 'users'));
    }

    /**
     * Update the specified dao tao staff
     */
    public function update(Request $request, DaoTao $daoTao)
    {
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:dao_tao,email,' . $daoTao->id,
            'so_dien_thoai' => 'nullable|string|max:15',
            'user_id' => 'nullable|exists:users,id',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('anh_dai_dien')) {
                // Delete old image
                if ($daoTao->anh_dai_dien && Storage::disk('public')->exists($daoTao->anh_dai_dien)) {
                    Storage::disk('public')->delete($daoTao->anh_dai_dien);
                }

                $image = $request->file('anh_dai_dien');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('dao_tao', $imageName, 'public');
                $validated['anh_dai_dien'] = $imagePath;
            }

            $daoTao->update($validated);

            DB::commit();
            return redirect()->route('admin.dao-tao.index')
                ->with('success', 'Cập nhật nhân viên đào tạo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified dao tao staff (soft delete)
     */
    public function destroy(DaoTao $daoTao)
    {
        try {
            $daoTao->delete();
            return redirect()->route('admin.dao-tao.index')
                ->with('success', 'Xóa nhân viên đào tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Assign user to dao tao staff
     */
    public function assignUser(Request $request, DaoTao $daoTao)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $daoTao->update(['user_id' => $validated['user_id']]);
            return redirect()->back()
                ->with('success', 'Gán user thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Unassign user from dao tao staff
     */
    public function unassignUser(DaoTao $daoTao)
    {
        try {
            $daoTao->update(['user_id' => null]);
            return redirect()->back()
                ->with('success', 'Hủy gán user thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
