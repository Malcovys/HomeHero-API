<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\User;
class UserController extends Controller
{
        //Afficher les taches d'un foyer
        public function allUser() {

            $allUser = User::select("id", "name", "email")->where('foyer_id', null)->orderBy('name', 'desc')->get();

            return response(
                [
                    'users' => $allUser
                ]
            );
        }
        public function allMembre(int $id) {

            $allUser = User::select("id", "name", "email")->where('foyer_id', $id)->orderBy('name', 'desc')->get();

            return response(
                [
                    'users' => $allUser
                ]
            );
        }


        // Désactiver un utilisateur
        public function active(Request $request) {

            $validated = $request->validate([
                'userId' => 'required|int',
            ]);

            $user = User::Where("id",$validated["userId"])->first();
        
            if (!$user) {
                return response([
                    'message' => "Cet utilisateur n'existe pas",
                ],403);
            }
            else if($user->foyer_id == null) {
                return response([
                    'message' => "Cet utilisateur n'est pas dans ce foyer",
                ],403);
            }
            else if($user->foyer->admin_id !== auth()->user()->id) {
                return response([
                    'message' => "L'utislisateur n'a pas accès à cela",
                ],403);
            }

            $user->update([
                "active" => !$user->active,
            ]);
            
            return response([
                "user" => $user->active
            ],200);
        }
}
