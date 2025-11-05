<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/home');
        }
        
        return view('auth.login');
    }

    // Show register form
    public function showRegisterForm()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/home');
        }
        
        return view('auth.register');
    }

    // Handle login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Clear any intended URL to prevent wrong redirects
            $request->session()->forget('url.intended');
            
            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard')->with('success', 'Welcome back, Admin!');
            }
            
            return redirect('/home')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    // Handle register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
        ], [
            'name.unique' => 'Username telah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus menggunakan format yang valid (contoh: user@example.com)',
            'email.unique' => 'Email telah digunakan',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan kecil',
            'password.numbers' => 'Password harus mengandung angka'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_type', 'validation');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default to user
        ]);

        Auth::login($user);
        
        $request->session()->regenerate();

        return redirect('/home')->with('success', 'Registration successful! Welcome to CV Analyzer.');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout successful!');
    }
}