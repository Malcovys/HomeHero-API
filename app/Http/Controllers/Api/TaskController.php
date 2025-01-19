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
        $taskQueue = new \SplQueue();
        $userQueue = new \SplQueue();
        $house_id = Auth::user()->house_id;
        $userTasks = []; // Array to store user tasks

        /* Get all tasks that are active
        And set tasks to queue */
        $tasks = Task::where('house_id', $house_id)
                ->whereNot('is_active', false)
                ->get(['id', 'frequency', 'required_member']);
        foreach($tasks as $task) {
            $taskQueue->enqueue($task);
        }

        /* Get all users that are present
        And user ids to queue */
        $users = User::where('house_id', $house_id)
                ->whereNot('present', false)
                ->get(['id']);
        foreach($users as $user) {
            $userQueue->enqueue($user->id);
        }        

        // Assign tasks to users
        foreach(range(1, 14) as $day) {
            foreach($taskQueue as $task) {
                if(($day) % $task->frequency == 0) {
                    for($i=0; $i < $task->required_member; $i++) {
                        $current_user = $userQueue->dequeue(); // Get current user

                        $userTasks[] = [
                            'task_id' => $task->id,
                            'user_id' => $current_user,
                            'day' => $day
                        ];

                        $userQueue->enqueue($current_user); // Return user to queue
                    }
                }
            }
        }

        return response()->json(['userTasks' => $userTasks]);
    }
}
