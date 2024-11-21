<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class HouseController extends Controller
{
    public function createHouse(Request $request) {
        try {
            $house = $request->validate([
                'name' => 'required|max:55|unique:houses',
            ]);

            // Only users haven't house can create one
            $user_id = Auth::id();
            $user = User::find($user_id)->where('house_id', null)->first();
            
            if (!$user) {
                return response()->json(['user' => 'User already has a house.'], 401);
            }

            // House creation
            $houseData = House::create($house);
            // First house role creation 
            $role = Role::create([
                'name' => 'admin',
                'house_id' => $houseData->id,
                'namage_priv_priv' => true,
                'manage_priv_priv' => true,
                'manage_house_priv' => true,
                'manage_member_priv' => true,
                'manage_task_priv' => true,
                'manage_even_priv' => true,
            ]);

            // Assing house to creator
            $user->house_id = $houseData->id;
            $user->role_id = $role->id;
            $user->save();
                
            return response()->json(['message' => 'House created successfully.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], status: 400);
        }
    }
    
    public function renameHouse(Request $request) {

    }

    public function deleteHouse(Request $request) {

    }

    public function addMemember(Request $request) {
        try {
            if(Auth::user()->role == 'admin') { //
                $members = $request->validate(rules: [
                    'house_id' => 'required|exists:houses,id',
                    'user_ids*' => 'required|exists:users,id',
                ]);
                
                // House assignation to users
                $unadded_user_id = [];
                foreach($members["user_ids"] as $user_id) {
                    $user = User::find($user_id)->where('house_id', null)->first();
                    
                    // Onli homeless user is authorized
                    if($user) {
                        $user->house_id =  $members["house_id"];
                        $user->save();
                    } else {
                        $unadded_user_id[] = $user_id;
                    }
                }

                return response()->json([
                    "unadded" => [
                        "count" =>count( $unadded_user_id),
                        "users_id" => $unadded_user_id
                    ],
                    "message" => count( $unadded_user_id) ?? "Users doesn't exist or already have house assigned.",
                ]);
            }

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], status: 400);
        }
    }

    public function deactiveMemember(Request $request) { 
        try {
            $to_deactivate_user= $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            // User and admin need to in same house
            $user = User::find($to_deactivate_user["user_id"])
                        ->where('house_id', Auth::user()->house_id)
                        ->first();

            if(!$user) {
                return response()->json(["error" => "User not house member."], );//bad request
            }

            // Only house admin can deactive user
            if(Auth::user()->role != 'admin') {
                return response()->json(['error' => 'Unthorized operation.'], 401);
            }
            
            $user->activate = false;
            $user->save();

            return response()->json(['message' => 'User deactivated with success.'], 401);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], status: 400);
        }
    }

    public function activeMember(Request $request) {

    }

    public function removeMember(Request $request) { 

    }
}
