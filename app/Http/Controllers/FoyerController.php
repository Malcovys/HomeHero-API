<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foyer;
use App\Models\User;

class FoyerController extends Controller
{
    //Afficher tous les foyers
    public function index() {
        return response(
            [
                'foyers' => Foyer::orderBy('name', 'desc')->get()
            ]
        );
    }

    // Créer un foyer
    public function store(Request $request) {
        // validation
        $valid = $request->validate([
            'name' => 'required|string',
        ]);

        // Créer un foyer
        $foyer = Foyer::create([
            'name' => $valid['name'],
            
            //Récupérer l'id de l'utilisateur connecté
            'admin_id' => auth()->user()->id
        ]);

        auth()->user()->foyer_id = $foyer->id;
        auth()->user()->save(); 

        // retourner le foyer
        return response([
            'message' => 'Foyer created',
            'foyer' => $foyer
        ],200);
    }

    // Mettre à jour un foyer
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

        // retourner le foyer
        return response([
            'message' => 'Foyer updated',
            'foyer' => $foyer
        ],200);
    }

    // Supprimer le foyer
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

        // retourner le foyer
        return response([
            'message' => 'Foyer supprimé',
        ],200);
    }
}
