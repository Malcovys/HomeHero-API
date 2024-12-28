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
    public function getAll() {
        $houses = House::get();
        return response()->json(['houses' => $houses]);
    }
    public function create(Request $request) {
        try {
            $house = $request->validate([
                'name' => 'required|unique:houses',
            ]);
        } catch(\Exception $exception) {
            return response()->json(['error'=> $exception->getMessage()], $exception->getCode());
        }

        // Only users haven't house can create one
        $user = User::find(Auth::id())->whereNull('house_id')->first();
        if (!$user) {
            return response()->json(['error' => 'User already have a house.'], 403);
        }

        // House creation
        DB::transaction(function () use ($house, $user) {
            $houseData = House::create($house);

            // Create role master and member
            $role_master = Role::create([
                'name' => 'master',
                'house_id' => $houseData->id,
                'manage_role_priv' => true,
                'manage_house_priv' => true,
                'manage_member_priv' => true,
                'manage_task_priv' => true,
            ]);
            Role::create([
                'name' => 'member',
                'house_id' => $houseData->id,
            ]);

            // Assing house and role master to user
            $user->update([
                'house_id' => $houseData->id,
                'role_id' => $role_master->id,
            ]);
        });
    }
}
