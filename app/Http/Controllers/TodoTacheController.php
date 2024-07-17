<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\Foyer;
use App\Models\User;
use App\Models\Historique;
use Carbon\Carbon;

class TodoTacheController extends Controller
{
    // Tache à faire
    public function todoTache(Request $request,int $id) {
        $valid = $request->validate([
            'date' => 'required|int',
        ]);

        $foyer = Foyer::find($id);


        if($id !== auth()->user()->foyer_id) {
            return response([
                'message' => "L'utislisateur n'a pas accès à cela",
            ],403);
        }

        if (!$foyer) {
            return response([
                'message' => 'Foyer inconnu',
            ],403);
        }
    
    
        return response(
            $this->getUserTache($id, $valid['date']-1),
            200
        );
    }


    //Obtenir la tache d'un utilisateur pendant un jour
    public function getUserTache($foyer_id, $date) {
        $allTache = Tache::orderBy('name', 'asc')->select('id', 'name', 'color')->where('foyer_id','=', $foyer_id)->get();
        $allUser = User::orderBy('id', 'asc')->where('foyer_id','=', $foyer_id)->where('active', true)->get();

        $nbrTache = $allTache->count();
        $nbrUser = $allUser->count();
        $newAllTache = [];

        $date = ($date > $nbrUser) ? ($date % $nbrUser) : $date;
        
        // Réorganiser les taches
        if($nbrTache <= $nbrUser) {
            foreach ($allUser as $k => $user) {
                $newAllTache[$k] = isset($allTache[$k]) ? $allTache[$k]->id. '-' .$allTache[$k]->name. '-' .$allTache[$k]->color : "-1-Vous n'avez pas de tache 😊-4279793650";

            }

        }
        else{
            foreach ($allTache as $index => $tache) {
                $userIndex = ($index % $nbrUser) + 1;
            
                if (!isset($newAllTache[$userIndex])) {
                    $newAllTache[$userIndex] = [];
                }
                $newAllTache[$userIndex][] = $tache->id. '-' .$tache->name. '-' .$tache->color;
            }
            $date++;
        }



        //Pendant un jour, il se peut que cetains utilisateurs n'ont pas de tache si le nbr de user > nbr tache
        //Dans le cas contraire, tous les utilisateurs ont un ou plusieurs taches par jour
        foreach ($allUser as $key => $user) {

            if(!isset($newAllTache[$key + $date])){
                $result[]= [
                    "user" => [
                        "id" =>$user->id,
                        "name" =>$user->name
                    ],
                    "state" => $this->checkInHistorique($user->id),

                    // "tache" =>($key + $date - $nbrUser >= 0) ? $newAllTache[$key + $date - $nbrUser] : ["Un imprévu est survenu."]
                    "tache" => $nbrTache <= $nbrUser 
                        ? (($key + $date - $nbrUser >= 0) ? [$newAllTache[$key + $date - $nbrUser]] : ["Un imprévu est survenu."])
                        : (($key + $date - $nbrUser >= 0) ? $newAllTache[$key + $date - $nbrUser] : ["Un imprévu est survenu."])

                ];
            }
            else{
                $result[]= [
                    "user" => [
                        "id" =>$user->id,
                        "name" =>$user->name
                    ],
                    "state" => $this->checkInHistorique($user->id),

                    "tache" => $nbrTache <= $nbrUser 
                        ? [$newAllTache[$key + $date]]
                        : $newAllTache[$key + $date]
                    
                ];
               
            }

        }
        return $result;
        
    }


    public function checkInHistorique(int $userId) {
        $historique = Historique::
        Where("user_id", $userId)
        ->whereDate("created_at", Carbon::today())
        ->first();

        return $historique?true:false;
    }


}
