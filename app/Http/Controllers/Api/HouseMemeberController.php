<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class HouseMemeberController extends Controller
{
    public function getAll(int $house_id) {
        $authorized_action = Auth::user()->house_id == $house_id;
        if(!$authorized_action) {
            return response()->json(["error" => "User is not house memeber."], 401);
        }

        $members = User::where('house_id', $house_id)->get();

        return response()->json(['members' => $members]);
    }
    
    public function add(int $house_id, int $user_id) {
        $authorized_action = User::select('roles.manage_member_priv')
                            ->join('roles', 'users.role_id', '=', 'roles.id')
                            ->where('users.id', Auth::user()->id)
                            ->where('users.house_id', $house_id)
                            ->first();

        if(!$authorized_action) {
            return response()->json(["error" => "User haven't required privilege."], 401);
        }

        $user = User::find($user_id)
                    ->whereNull('house_id')
                    ->first();

        if(!$user) {
            return response()->json(["error" => "User already have a house."], 400);//bad request
        }

        $user->house_id = $house_id;
        $user->save();
    }
}
