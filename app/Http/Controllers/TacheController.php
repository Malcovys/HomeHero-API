<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
class TacheController extends Controller
{
        //Afficher les taches d'un foyer
        public function index($id) {

            $foyer = Foyer::find($id);
    
            return response(
                [
                    'foyers' => $foyer->taches()->orderBy('name', 'desc')->get()
                ]
            );
        }
    
        // Créer une tache
        public function store(Request $request) {
            // validation
            $valid = $request->validate([
                'name' => 'required|string',
            ]);
    
            $foyer = Tache::create([
                'name' => $valid['name'],
                
                'admin_id' => auth()->user()->id
            ]);
    
            return response([
                'message' => 'Foyer created',
                'foyer' => $foyer
            ],200);
        }
    
        // Mettre à jour une tache
        public function update(Request $request, $id) {
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
    
            $foyer -> update([
                'name' => $valid['name'],
            ]);
    
            return response([
                'message' => 'Foyer created',
                'foyer' => $foyer
            ],200);
        }
    
        // Supprimer la tache
        public function delete($id){
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
    
            $foyer -> delete();
    
            return response([
                'message' => 'Foyer supprimé',
            ],200);
        }
}
