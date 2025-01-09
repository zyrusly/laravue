<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        info($credentials);
        if (Auth::attempt($credentials)) {
            return response(Auth::user(), 200);
        }
        return response()->json(['error' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::logout();
        return response(null, 200);
    }
}
