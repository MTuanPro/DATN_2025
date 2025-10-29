<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalRoles' => VaiTro::count(),
            'activeUsers' => User::where('trang_thai', 1)->count(),
            'todayLogs' => 0, // TODO: Implement log counting
        ];

        return view('admin.dashboard', $data);
    }
}
