<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\Foyer;
use App\Models\User;
use App\Models\Historique;
use App\Models\Groupe;
use Carbon\Carbon;

class TodoTacheController extends Controller
{
    // Tache à faire
    public function todoTache(Request $request,int $id) {
        $valid = $request->validate([
            'date' => 'required|int',
        ]);

        $foyer = Foyer::find($id);


        if($id !== auth()->user()->foyer_id || auth()->user()->foyer_id == null) {
            return response([
                'message' => "unauthorized",
            ],403);
        }

        if (!$foyer) {
            return response([
                'message' => 'Foyer inconnu',
            ],404);
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
        // $allGroupe = Groupe::orderBy('id', 'asc')->where('foyer_id','=', $foyer_id)->get();
        

        $allGroupe = Groupe::orderBy('id', 'asc')
        ->where('foyer_id', '=', $foyer_id)
        ->whereHas('users', function ($query) {
            $query->whereColumn('groupe_id', 'groupes.id');
        })
        ->get();

        
        
        if($allGroupe->isNotEmpty()){
            // $userNotInGroupe = User::orderBy('id', 'asc')
            //     ->where('foyer_id','=', $foyer_id)
            //     ->where('groupe_id', null)
            //     ->where('active', true)
            //     ->get();

            // $allUser = $allGroupe;
            $userNotInGroupe = User::orderBy('id', 'asc')
                ->where('foyer_id', '=', $foyer_id)
                ->where('groupe_id', null)
                ->where('active', true)
                ->get();

            // Fusionner dans les utilisateurs, la liste des groupes et ceux qui ne sont pas dans un groupe
            $allUser = $allGroupe->merge($userNotInGroupe);
        }
        
        $nbrTache = $allTache->count();
        $nbrUser = $allUser->count();
        $newAllTache = [];

        $date = ($date > $nbrUser) ? ($date % $nbrUser) : $date;
        
        // Réorganiser les taches
        if($nbrTache <= $nbrUser) {
            foreach ($allUser as $k => $user) {
                $newAllTache[$k] = isset($allTache[$k]) ? $allTache[$k]->id. '-' .$allTache[$k]->name. '-' .$allTache[$k]->color : "_1-Vous n'avez pas de tache 😊-4279793650";

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
                        "name" =>$user->name,
                        "usersIdInGroupe" => $allGroupe->isNotEmpty() ? 
                            isset($allGroupe[$key])?
                                $allGroupe[$key]->users()->pluck("id") 
                                :[$user->id]
                        : [$user->id],

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
                        "name" =>$user->name,
                        // "usersIdInGroupe" => $allGroupe->isNotEmpty() ? $allGroupe[$key]->users()->pluck("id") : [$user->id],
                        "usersIdInGroupe" => $allGroupe->isNotEmpty() ? 
                                                    isset($allGroupe[$key])?
                                                        $allGroupe[$key]->users()->pluck("id") 
                                                        :[$user->id]
                                                : [$user->id],
                    ],
                    "state" => $this->checkInHistorique($user->id),


                    "tache" => $nbrTache <= $nbrUser 
                        ? [$newAllTache[$key + $date]]
                        : $newAllTache[$key + $date]
                    
                ];
               
            }

        }
        //Réorganiser pour afficher l'user connecté en haut
        $connectedUserId = auth()->id(); 
        usort($result, function($a, $b) use ($connectedUserId) {
            $aHasUserId = in_array($connectedUserId, collect($a['user']['usersIdInGroupe'])->toArray());
            $bHasUserId = in_array($connectedUserId, collect($b['user']['usersIdInGroupe'])->toArray());

            return $bHasUserId <=> $aHasUserId;
        });
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
