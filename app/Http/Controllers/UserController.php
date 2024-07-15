<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\User;
use App\Models\Foyer;
class UserController extends Controller
{
        //Afficher les utilisateurs
        public function allUser() {

            $allUser = User::select("id", "name", "email", "active", "profil")->where('foyer_id', null)->orderBy('name', 'desc')->get();

            return response(
                [
                    'users' => $allUser
                ]
            );
        }
        //Afficher tous les membres du foyer
        public function allMembre(int $id) {

            $allUser = User::select("id", "name", "email", "active", "profil")->where('foyer_id', $id)->orderBy('name', 'desc')->get();

            return response(
                [
                    'users' => $allUser
                ]
            );
        }


        // Activer ou Désactiver un utilisateur
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
                "user" => $user
            ],200);
        }

        //Changer l'admin du foyer
        public function changeAdmin(Request $request) {
            $validated = $request->validate([
                'userId' => 'required|int',
            ]);

            $user_id = auth()->user()->id;

            $foyer = Foyer::Where("admin_id", $user_id)->first();

            if (!$foyer) {
                return response([
                    'message' => "Cet utilisateur n'existe pas",
                ],403);
            }
            $foyer->update([
                "admin_id" => $validated["userId"]
            ]);
            return response([
                "foyer" => $foyer
            ],200);
        }

        public function updateUserPreference(Request $request) {
            $validated = $request->validate([
                'mode' => 'required',
            ]);

            $user = User::where('id', auth()->user()->id)->first();
            $user->update([
                "mode" => ($validated["mode"] == "true")?1:0
            ]);
            return response([
                "mode validé" => $validated["mode"],
                "user" => $user,
            ],200);
        }

        public function updateUser(Request $request) {
            $validated = $request->validate([
                'profil' => 'required',
            ]);

            $user = User::where('id', auth()->user()->id)->first();
            $user->update([
                "profil" => $validated["profil"]
            ]);
            return response([
                "profil" => $validated["profil"],
                "user" => $user,
            ],200);
        }
}
