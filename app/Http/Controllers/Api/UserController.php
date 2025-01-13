<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function searchHouselessUser(Request $request) {
        $query = $request->query("q");

        $reuslts = array();
        $to_selected_user_data = ['id', 'name', 'email', 'photo_url', 'created_at'];
        if(!$query) {
            $reuslts = User::where('house_id', null)
                        ->get($to_selected_user_data);
        } else {
            $reuslts = User::whereRaw(
                        'LOWER(name) LIKE ? AND LOWER(name) NOT LIKE ?',
                        [
                            '%'.strtolower($query).'%',
                            strtolower(Auth::user()->name)
                        ])->where('house_id', null)
                        ->get($to_selected_user_data);
        }

        return response()->json(['results' => $reuslts]);
    }

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
        try {
            $credential = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch(\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    
        if (!Auth::attempt($credential)) {
            return response()->json(['error' => "Invalid email or password"], 403);
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
