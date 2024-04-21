<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login_controller extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            // User is logged in
            $user = Auth::user(); // This will give you the currently authenticated user instance
            return redirect('dashboard');
        } else {
            return view('login');
        }
        
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $remember = $request->remember;
        // dd($remember);
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => 1], $remember)) {
            // Authentication was successful...
            return redirect('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
