<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalClasses' => 0, // TODO: Implement
            'totalStudents' => 0, // TODO: Implement
            'weekSessions' => 0, // TODO: Implement
            'pendingGrades' => 0, // TODO: Implement
            'homeRoomClass' => null, // TODO: Implement
        ];

        return view('giangvien.dashboard', $data);
    }
}
