<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function getHouseTasks(Request $request) {
    
    }
    
    public function create(Request $request) {
        $authorisation = Role::find(Auth::user()->role_id)
                            ->first(['manage_task_priv']);

        if(!$authorisation->manage_task_priv) {
            return response()->json(['error' => "User haven't permission to create task"], 403);
        }

        try {
            $task = $request->validate([
                'name' => 'required|string',
                'frequency' => 'required|int|min:1',
                'required_members' => 'required|int|min:1',
            ]);
        } catch(\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
        
    }

    public function delete(Request $request) {

    }

    public function rename(Request $request) { 

    }

    public function assing(Request $request) {

    }

    public function updateRequiredMember(Request $request) {

    }

    public function updateFrequency(Request $request) {

    }
}
