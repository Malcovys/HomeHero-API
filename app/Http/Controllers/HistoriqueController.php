<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\Historique;
use App\Models\User;

class HistoriqueController extends Controller
{
    public $foyer_id;

    // Marquer une tache comme réalisée
    public function historique(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|int',
            'taches' => 'required|array',
            'taches.*' => 'exists:taches,id', 
        ]);
    
        try {
            $historique = Historique::create([
                'user_id' => $validated['user_id'],
            ]);
    
            $historique->taches()->attach($validated['taches']);
    
            return response()->json([
                'message' => 'Historique créé avec succès et tâches ajoutées',
                'historique' => $historique->load('taches'), 
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l\'historique',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Obtenir les historique à confirmer
    public function confirmation()
    {
        $this->foyer_id = auth()->user()->foyer_id;

        // $historique = Historique::with("user","taches")->get();
        // $historique = Historique::join('users', 'historiques.user_id', '=', 'users.id')
        //                         ->where('users.foyer_id', $this->foyer_id)
        //                         ->with(['user', 'taches'])
        //                         ->get(['historiques.*']);

        $historique = Historique::join('users', 'historiques.user_id', '=', 'users.id')
                                ->where('users.foyer_id', $this->foyer_id)
                                ->with(['user:id,name', 'taches:id,name'])
                                ->get(['historiques.*']);



        return response()->json($historique, 200);

    }
    
}
