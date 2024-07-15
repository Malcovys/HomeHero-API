<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\Historique;
use App\Models\User;
use Carbon\Carbon;

class HistoriqueController extends Controller
{
    public $user_confirm_id, $foyer_id;

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
    public function Listconfirmation()
    {
        $this->foyer_id = auth()->user()->foyer_id;


    $historique = Historique::join('users', 'historiques.user_id', '=', 'users.id')
        ->where('users.foyer_id', $this->foyer_id)
        ->whereDate('historiques.created_at', Carbon::today())
        ->with(['user:id,name,email', 'taches'])
        ->get(['historiques.id', 'historiques.user_id', 'historiques.state']);



        return response()->json($historique, 200);

    }

    public function confirmer(Request $request)
    {
        $validated = $request->validate([
            'historique_id' => 'required|int',
        ]);

        $historique = Historique::Where("id", $validated['historique_id'])->first();
        
        if (!$historique) {
            return response([
                'message' => 'Historique inexistante',
            ],403);
        }

        $historique->update([
            "state" => true,
            "user_confirm_id" => auth()->user()->id
        ]);

        return response([
            "historique" => $historique
        ],200);
    }
    
}
