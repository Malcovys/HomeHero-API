<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
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

            $houseData = House::create($house);

            $user_id = Auth::id();
            $user = User::find($user_id)->where('house_id', null);

            if (!$user) {
                return response()->json(['user' => 'User already has a house'], 401);
            }

            $user->house_id = $houseData->id;
            $user->save();
                
            return response()->json(['message' => 'House created successfully'], 201);
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], status: 400);
        }
    }
    
    public function renameHouse(Request $request) {

    }

    public function deleteHouse(Request $request) {

    }

    public function addMemember(Request $request) {

    }

    public function deactiveMemember(Request $request) { 

    }

    public function activeMember(Request $request) {

    }

    public function removeMember(Request $request) { 

    }
}
