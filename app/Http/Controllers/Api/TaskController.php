<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;
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

    public function scheduleNextWeekTasks() {
        $userTasks = array();
        $house_id = Auth::user()->house_id;
        $last_task_id = 0;
        $last_user_id = 0;

        $last_userTask = UserTask::select(['user_tasks.task_id', 'user_tasks.user_id', 'user_tasks.day'])
                ->join('tasks', 'user_tasks.task_id', '=', 'tasks.id')
                ->where('tasks.house_id', $house_id)
                ->orderBy('user_tasks.day', 'desc')
                ->first();
        
        if($last_userTask) {
            $last_task_id = $last_userTask->task_id;
            $last_user_id = $last_userTask->user_id;
        }

        $taskQueue = $this->getTaskQueue($house_id, $last_task_id);
        $userQueue = $this->getUserQueue($house_id, $last_user_id);     

        // Assign tasks to users
        foreach(range(1, 7) as $day) {
            foreach($taskQueue as $task) {
                if(($day) % $task->frequency == 0) {
                    for($i=0; $i < $task->required_member; $i++) {
                        $current_user = $userQueue->dequeue(); // Get current user
                        $userTasks[] = [
                            'task_id' => $task->id,
                            'user_id' => $current_user->id,
                            'day' => $day,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $userQueue->enqueue($current_user); // Return user to queue
                    }
                }
            }
        }

        UserTask::insert($userTasks);

        return response()->json(['userTasks' => $userTasks]);
    }

    /* Privates methods */

    private function getTaskQueue(int $house_id, int $last_task_id) {
        $tasks = Task::where('house_id', $house_id)
                ->where('id', '>', $last_task_id)
                ->where('is_active', true)
                ->get(['id', 'frequency', 'required_member']);
        if($last_task_id > 0) {
            $tasks = $tasks->merge(Task::where('house_id', $house_id)
                        ->where('id', '<=', $last_task_id)
                        ->where('is_active', true)
                        ->get(['id', 'frequency', 'required_member'])
                    );
        }

        $taskQueue = new \SplQueue();
        foreach($tasks as $task) {
            $taskQueue->enqueue($task);
        }
        
        return $taskQueue;
    }

    private function getUserQueue(int $house_id, int $last_user_id) {
        $users = User::where('house_id', $house_id)
                ->where('id', '>', $last_user_id)
                ->where('present', true)
                ->get(['id']);
        if($last_user_id > 0) {
            $users = $users->merge(User::where('house_id', $house_id)
                        ->where('id', '<=', $last_user_id)
                        ->where('present', true)
                        ->get(['id'])
                    );
        }

        $userQueue = new \SplQueue();
        foreach($users as $user) {
            $userQueue->enqueue($user);
        }

        return $userQueue;
    }
}
