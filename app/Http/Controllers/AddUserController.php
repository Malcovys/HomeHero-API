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
            'message' => $user,
        ],200);
    }

    //Supprimer un utilisateur du foyer
    public function deleteUser(int $id) {

        $user = User::find($id);
        
        if ($user->foyer_id== null) {
            return response([
                'message' => "Cet utilisateur n'est pas dans ce foyer",
            ],403);
        }
        if($user->foyer->admin_id !== auth()->user()->id) {
            return response([
                'message' => "L'utislisateur n'a pas accès à cela",
            ],403);
        }
        if (!$user) {
            return response([
                'message' => 'User inconnu',
            ],403);
        }

        $user->foyer_id = null;
        $user->save(); 

        return response([
            'message' => 'Utilisateur retiré du foyer',
        ],200);
    }
}
