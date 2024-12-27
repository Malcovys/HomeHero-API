<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class HouseMemeberController extends Controller
{
    public function getAll() {
        if(!Auth::user()->house_id) {
            return response()->json(['error' => "User haven't house."], 401);
        }

        $members = User::where('house_id', Auth::user()->house_id)
                    ->get();

        return response()->json(['members' => $members]);
    }
    
    public function add(int $user_id) {
        $authorized_action = User::select('roles.manage_member_priv')
                            ->join('roles', 'users.role_id', '=', 'roles.id')
                            ->where('users.id', Auth::user()->id)
                            ->first();

        if(!$authorized_action) {
            return response()->json(["error" => "User haven't required privilege."], 401);
        }

        $user = User::where('id', $user_id)
                    ->whereNull('house_id')
                    ->first();

        if(!$user) {
            return response()->json(["error" => "User already have a house."], 400);//bad request
        }

        $user->house_id = Auth::user()->house_id;
        $user->save();
    }
}
