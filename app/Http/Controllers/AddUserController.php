<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Groupe;

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


    public function createGroupe(Request $request) {
        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'usersId' => 'array|required',  
        ]);
    
        // Création du groupe 
        $groupe = Groupe::create([
            'name' => $valid['name'],  
            'foyer_id' => auth()->user()->foyer_id,  
        ]);
    
        // Attribution des utilisateurs au groupe
        foreach ($valid['usersId'] as $user_id) {
            $user = User::find($user_id);
            if (!$user) {
                return response()->json([
                    'message' => 'Utilisateur inconnu',
                ], 404); 
            }
    
            $user->groupe_id = $groupe->id;
            $user->save(); 
        }
    
        return response([
            'id' => $groupe->id,
            'name' => $groupe->name,
            'image' => $groupe->users()->pluck("profil"),
            'nbrMembres' => count($groupe->users()->get()),
        ], 200);
    }

    public function getListGroupe() {

        $groupes = Groupe::where('foyer_id', auth()->user()->foyer_id)->orderBy('id', 'asc')->get();

        foreach($groupes as $groupe){
            $data[] = [
                'id' => $groupe->id,
                'name' => $groupe->name,
                'image' => $groupe->users()->pluck("profil"),
                'nbrMembres' => count($groupe->users()->get()),
            ];
        }

        return response(
            $data
        );
    }
    


    //Supprimer un utilisateur du foyer
    public function removeUser(Request $request) {
        $validated = $request->validate([
            'userId' => 'required|int',
        ]);

        $user = User::find($validated['userId']);
        
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

        $user->update([
            "foyer_id" => null,
            "groupe_id" => null,
            "active" => true
        ]);

        return response([
            'message' => 'Utilisateur retiré du foyer',
            "user" => $user
        ],200);
    }
}
