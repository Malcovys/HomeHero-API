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
    public function create(Request $request) {
        try {
            $house = $request->validate([
                'name' => 'required|unique:houses',
            ]);
        } catch(\Exception $exception) {
            return response()->json(['error'=> $exception->getMessage()], 400);
        }

        // Only users haven't house can create one
        $user = User::find(Auth::id())->whereNull('house_id')->first();
        if (!$user) {
            return response()->json(['error' => 'User already have a house.'], 401);
        }

        // House creation
        DB::transaction(function () use ($house, $user) {
            $houseData = House::create($house);

            // First house role creation 
            $role = Role::create([
                'name' => 'master',
                'house_id' => $houseData->id,
                'manage_role_priv' => true,
                'manage_house_priv' => true,
                'manage_member_priv' => true,
                'manage_task_priv' => true,
            ]);

            // Assing house to creator
            $user->update([
                'house_id' => $houseData->id,
                'role_id' => $role->id,
            ]);
        });
    }
    
    // public function rename(Request $request) {
    //     $authorized_action = Role::find(Auth::user()->role_id)->get(['manage_house_priv']);
        
    //     if(!$authorized_action) { 
    //         abort(401,"User haven't required privilege.");
    //     }

    //     $houseData = $request->validate([
    //         'new_name' => 'required|unique:houses',
    //     ]);

    //     $house = House::find(Auth::user()->house_id)->first();
    //     if(!$house) {
    //         abort(401,"User haven't house.");
    //     }
        
    //     $house->update([
    //         'name' => $houseData['new_name']
    //     ]);

    //     return response()->json(['message' => 'House renamed with success.']);
    // }
}
