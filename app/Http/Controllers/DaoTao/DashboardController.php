<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalStudents' => 0, // TODO: Implement
            'totalTeachers' => 0, // TODO: Implement
            'totalClasses' => 0, // TODO: Implement
            'totalSubjects' => 0, // TODO: Implement
            'warningsAcademic' => 0, // TODO: Implement
            'warningsTuition' => 0, // TODO: Implement
        ];

        return view('daotao.dashboard', $data);
    }
}
