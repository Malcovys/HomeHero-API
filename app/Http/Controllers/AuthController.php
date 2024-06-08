<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Register
    public function register(Request $request) {
        //validation
        $valid = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Créer un utilisateur
        $user = User::create([
            'name' => $valid['name'],
            'email' => $valid['email'],
            'password' => bcrypt($valid['password']),
        ]);

        // retourner l'utilisateur et le token
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ]);
    }

    // Login
    public function login(Request $request) {
        //validation
        $valid = $request->validate([
            'email' => 'required|email',
            'mdp' => 'required|min:6',
        ]);

        if (!Auth::attempt($valid)) {
            return response([
                'message' => 'Invalid credentials',
            ], 403);
        }

        // retourner l'utilisateur et le token
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken,
        ]);
    }

    // logout
    public function logout() {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout succes',
        ], 200);
    }

    // Get user details
    public function user() {
        return response([
            'user' => auth()->user(),
        ], 200);
    }
}
