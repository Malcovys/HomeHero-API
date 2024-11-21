<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\Role;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HouseController extends Controller
{
    public function createHouse(Request $request) {
        $house = $request->validate([
            'name' => 'required|max:55|unique:houses',
        ]);

        // Only users haven't house can create one
        $user = User::find(Auth::id())->whereNull('house_id')->first();
        
        if (!$user) {
            abort(401, 'User already has a house.');
        }

        // House creation
        DB::Transacion(function () use ($house, $user) {
            $houseData = House::create($house);

            // First house role creation 
            $role = Role::create([
                'name' => 'master',
                'house_id' => $houseData->id,
                'nanage_priv_priv' => true,
                'manage_priv_priv' => true,
                'manage_house_priv' => true,
                'manage_member_priv' => true,
                'manage_task_priv' => true,
                'manage_even_priv' => true,
            ]);

            // Assing house to creator
            $user->update([
                'house_id' => $houseData->id,
                'role_id' => $role->id,
            ]);
        });
         
        return response()->json(['message' => 'House created successfully.']);
    }
    
    public function renameHouse(Request $request) {

    }

    public function deleteHouse(Request $request) {

    }

    public function addMemember(Request $request) {
        // check adder privilege
        $authorized_action = Role::find(Auth::user()->role_id)->get(['manage_member_priv']);

        if(!$authorized_action) { 
            abort(401,"User haven't required privilege.");
        }

        $members = $request->validate(rules: [
            'house_id' => 'required|exists:houses,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
        ]);
        
        // House assignation to users
        $unadded_user_id = [];
        foreach($members["user_ids"] as $user_id) {
            $user = User::find($user_id)->whereNull('house_id')->first();
            
            // Onli homeless user is authorized
            if($user) {
                $user->update([
                    "house_id" => $members["house_id"]
                ]);
            } else {
                $unadded_user_id[] = $user_id;
            }
        }

        return response()->json([
            "unadded" => [
                "count" =>count( $unadded_user_id),
                "users_id" => $unadded_user_id
            ],
            'message' => count($unadded_user_id) > 0 
                ? 'Some users could not be added because they already have a house.'
                : 'All users were added successfully.',
        ]);
    }

    public function deactiveMemember(Request $request) {
        $authorized_action = Role::find(Auth::user()->role_id)->get(['manage_member_priv']);

        if(!$authorized_action) {
            abort(401,"User haven't required privilege.");
        }

        $to_deactivate_user= $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // User and admin need to in same house
        $user = User::find($to_deactivate_user["user_id"])
                    ->where('house_id', Auth::user()->house_id)
                    ->first();

        if(!$user) {
            abort(400,"User not house member.");//bad request
        }
        
        $user->update([
            'activate' => false
        ]);

        return response()->json(['message' => 'User deactivated with success.'], 401);
    }

    public function activeMember(Request $request) {

    }

    public function removeMember(Request $request) { 

    }
}
