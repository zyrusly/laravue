<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
        info($credentials);
        if (Auth::attempt($credentials)) {
            return response(Auth::user(), 200);
        }
        return response()->json(['error' => 'Invalid credentials']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response(null, 200);
    }
}
