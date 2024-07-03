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
}
