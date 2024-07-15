<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\Foyer;
class TacheController extends Controller
{
        //Afficher les taches d'un foyer
        public function index($id) {

            $foyer = Foyer::find($id);
            if (!$foyer) {
                return response([
                    'message' => 'Foyer inconnu',
                ],403);
            }

            return response(
                [
                    'taches' => $foyer->tache()->select("id", "name", "color")->orderBy('id', 'desc')->get()
                ]
            );
        }
    
        // Créer une tache
        public function store(Request $request, $id) {
            // validation
            $valid = $request->validate([
                'name' => 'required|string',
                'color' => 'required',
            ]);

            $foyer = Foyer::find($id);

            if($foyer->admin_id !== auth()->user()->id) {
                return response([
                    'message' => "L'utislisateur n'a pas accès à cela",
                ],403);
            }

            if (!$foyer) {
                return response([
                    'message' => 'Foyer inconnu',
                ],403);
            }
    
            $tache = Tache::create([
                'name' => $valid['name'],
                'color' => $valid['color'],
                'foyer_id' => $id
            ]);
    
            return response(
                Tache::Where("id", $tache->id)->select("id", "name", "color")->first(),
                200
            );
        }
    
        // Mettre à jour une tache
        public function update(Request $request, $id) {
            // validation
            $valid = $request->validate([
                'name' => 'required|string',
            ]);
            $tache = Tache::find($id);
    
            if($tache->foyer->admin_id !== auth()->user()->id) {
                return response([
                    'message' => "L'utislisateur n'a pas accès à cela",
                ],403);
            }
            
            if (!$tache) {
                return response([
                    'message' => 'Tache inconnu',
                ],403);
            }
    
            $tache -> update([
                'name' => $valid['name'],
            ]);
    
            return response([
                'message' => 'Tache updated',
                'foyer' => $tache
            ],200);
        }
    
        // // Supprimer la tache
        public function delete(Request $request){
            $validated = $request->validate([
                'tacheId' => 'required|int',
            ]);

            $tache = Tache::find($validated["tacheId"]);
    
            if($tache->foyer->admin_id !== auth()->user()->id) {
                return response([
                    'message' => "L'utislisateur n'a pas accès à cela",
                ],403);
            }
    
            if (!$tache) {
                return response([
                    'message' => 'Tache inconnu',
                ],403);
            }
    
            $tache -> delete();
    
            return response([
                'message' => 'Tache supprimé',
            ],200);
        }
}
