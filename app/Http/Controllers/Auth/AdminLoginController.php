<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin) {
            return back()->withErrors([
                'username' => 'Invalid credentials.',
            ])->withInput();
        }

        // Verify password
        if (Hash::check($request->password, $admin->password)) {
            // Use custom guard for admin
            Auth::guard('admin')->login($admin, $request->filled('remember'));
            
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput();
    }

    /**
     * Handle admin logout request
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}

