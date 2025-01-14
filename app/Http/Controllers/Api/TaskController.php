<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function getHouseTasks(Request $request) {
        $tasks = Task::where('house_id', Auth::user()->house_id)
                ->get();
        return response()->json(['tasks' => $tasks]);
    }
    
    public function create(Request $request) {
        if(!Auth::user()->house_id) {
            return response()->json(['error' => "User haven't house"], 404);
        }

        $authorisation = Role::find(Auth::user()->role_id)
                            ->first(['manage_task_priv']);

        if(!$authorisation->manage_task_priv) {
            return response()->json(['error' => "User haven't permission to create task"], 403);
        }

        try {
            $task = $request->validate([
                'name' => 'required|string',
                'frequency' => 'required|int|min:1',
                'required_member' => 'required|int|min:1',
            ]);
        } catch(\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }

        Task::create([
            ...$task,
            'house_id' => Auth::user()->house_id
        ]);
    }

    public function delete(Request $request) {

    }

    public function assing() {
        $house_id = Auth::user()->house_id;
        $tasks = Task::where('house_id', $house_id)
                ->get(['id', 'frequency', 'required_member']);
        $users = User::where('house_id', $house_id)
                ->get(['id']);

        $userQueue = new \SplQueue();
        foreach($users as $user) {
            $userQueue->enqueue($user->id);
        }

        $userTasks = [];
        foreach($tasks as $task) {
            foreach(range(1, 7) as $day) {
                if(($day-1) % $task->frequency == 0) {
                    for($i=0; $i < $task->required_member; $i++) {
                        $current_user = $userQueue->dequeue();
                        $userTasks[] = [
                            'task_id' => $task->id,
                            'user_id' => $current_user,
                            'day' => $day
                        ];
                        $userQueue->enqueue($current_user);
                    }
                }
            }
        }
        return response()->json(['userTasks' => $userTasks]);
    }
}
