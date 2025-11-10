<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
            'notifications_email' => 'nullable|boolean',
            'notifications_browser' => 'nullable|boolean',
        ]);

        // Lưu settings vào session hoặc database
        session([
            'theme' => $request->theme,
            'notifications_email' => $request->has('notifications_email'),
            'notifications_browser' => $request->has('notifications_browser'),
        ]);

        return redirect()->route('settings.index')->with('success', 'Cài đặt đã được lưu!');
    }
}
