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
                    'foyers' => $foyer->taches()->orderBy('name', 'desc')->get()
                ]
            );
        }
    
        // Créer une tache
        public function store(Request $request, $id) {
            // validation
            $valid = $request->validate([
                'name' => 'required|string',
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
                'foyer_id' => $id
            ]);
    
            return response([
                'message' => 'Tache created',
                'tache' => $tache
            ],200);
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
        public function delete($id){
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
    
            $tache -> delete();
    
            return response([
                'message' => 'Tache supprimé',
            ],200);
        }
}
