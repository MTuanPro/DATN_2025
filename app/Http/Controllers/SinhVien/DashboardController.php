<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalCredits' => 0, // TODO: Implement
            'gpa' => '0.00', // TODO: Implement
            'currentClasses' => 0, // TODO: Implement
            'debt' => 0, // TODO: Implement
            'studentCode' => null, // TODO: Implement
            'className' => null, // TODO: Implement
            'course' => null, // TODO: Implement
            'warnings' => [], // TODO: Implement
        ];

        return view('sinhvien.dashboard', $data);
    }
}
