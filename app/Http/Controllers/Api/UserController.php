<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request) {
        try {
            $user = $request->validate([
                'name' => 'required|unique:users',
                'email' => 'email|required|unique:users',
                'password' => 'required',
            ]);
        } catch(\Exception $exception) {
            return response()->json(['error'=> $exception->getMessage()], 400);
        }

        $user['password'] = \Illuminate\Support\Facades\Hash::make($user['password']);

        User::create($user);
    } 

    public function login(Request $request) {
        $auth = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (!Auth::attempt($auth)) {
            abort(401, "Invalid email or password");
        }
    
        $user = Auth::user();
    
        $token = $user->createToken('API Token')->plainTextToken;
    
        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $user->image,
                'token' => $token
            ]
        ]);
    }

    public function logout() {
        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
