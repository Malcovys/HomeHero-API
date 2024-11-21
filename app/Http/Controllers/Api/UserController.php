<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request) {
        $user = $request->validate([
            'name' => 'required|max:55|unique:users',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user['password'] = \Illuminate\Support\Facades\Hash::make($user['password']);

        User::create($user);

        return response()->json(['message' => 'User created successfully']);
    } 

    public function login(Request $request) {
        $auth = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (!Auth::attempt($auth)) {
            return response()->json(['authenticationError' => 'Invalid email or password'], 401);
        }
    
        $user = Auth::user();
    
        $token = $user->createToken('API Token')->plainTextToken;
    
        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $token
            ]
        ]);
    }

    public function logout() {
        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
