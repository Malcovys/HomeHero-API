<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foyer;
use App\Models\User;

class FoyerController extends Controller
{
    //
    public function foyer(Request $request) {
        //validation
        // $valid = $request->validate([
        //     'name' => 'required|string',
        // ]);

        // // Créer un foyer
        // $foyer = Foyer::create([
        //     'name' => $valid['name'],
        // ]);

        // retourner le foyer
        $user = User::find(1);
        $foyer = Foyer::find(2);
        return response([
            'foyer' => $user->foyer,
            'user' => $foyer->user,
            // 'token' => $user->createToken('secret')->plainTextToken,
        ]);
    }
}
