<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AddUserController extends Controller
{
    //Ajouter des utilisateurs dans un foyer
    public function addUser(Request $request,int $id) {
        $users_id = $request->input();


        foreach ($users_id as $user_id) {
            $user = User::find($user_id);
            if (!$user) {
                return response([
                    'message' => 'Utilisateur inconnu',
                ], 404); 
            }
            $user->foyer_id = $id;
            $user->save(); 
        }
        
        

        return response([
            'id' => $users_id,
        ],200);
    }
}
