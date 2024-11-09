<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HouseController extends Controller
{
    public function createHouse(Request $request) {
        try {
            $house = $request->validate([
                'name' => 'required|max:55|unique:houses',
            ]);

            House::create($house);

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
