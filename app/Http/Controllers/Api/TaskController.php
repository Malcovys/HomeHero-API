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
        // Get house id
        $house_id = Auth::user()->house_id;

        // Get all tasks that are active
        $tasks = Task::where('house_id', $house_id)
                ->whereNot('is_active', false)
                ->get(['id', 'frequency', 'required_member']);

        // Get all users that are present
        $users = User::where('house_id', $house_id)
                ->whereNot('present', false)
                ->get(['id']);

        // Create a queue of user ids
        $userQueue = new \SplQueue();
        foreach($users as $user) {
            $userQueue->enqueue($user->id);
        }

        // Create a queue of task
        $taskQueue = new \SplQueue();
        foreach($tasks as $task) {
            $taskQueue->enqueue($task);
        }

        // Create an array to store user tasks
        $userTasks = [];

        // Assign tasks to users
        foreach(range(1, 7) as $day) {
            foreach($tasks as $task) {
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
