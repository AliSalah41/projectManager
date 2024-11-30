<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $task = Task::create([
            "name" => $request->name,
            "description" => $request->description ?? null,
            "status" => $request->status,
            'priority' => $request->priority ?? null,
            'project_id' => $request->project_id,
            'parent_task_id' => $request->parent_task_id ?? null,
            'assigned_user_id' => $request->assigned_user_id ?? null,
            "start_date" => $startDate ?? null,
            "end_date" => $endDate ?? null,
        ]);

        if($request->assigned_user_id)
        {
            $user = User::find($request->assigned_user_id);
            if(!$user)
            {
                return response()->json([
                    'message' => 'User not found',
                    'status' => false,
                ],404);    
            }
            $user->sendTaskAssignedNotification($task);
        }

        if($task)
        {
            return response()->json([
                'message' => 'Task successfully created',
                'status' => true,
            ],201);
        }

       

        else{
            return response()->json([
                'message' => 'something went wrong',
                'status' => false,
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::with([
            'subtasks',    
            'parentTask',  
            'dependencies' 
        ])->findOrFail($id);
    
       
        if($task)
        {
            return response()->json([
                'status' => true,
                'message' => 'Task retrieved successfully',
                'data' =>new TaskResource($task),
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'Task not found',
                'status' => false,
            ],404); 
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found.',
                'status' => false,
            ], 404);
        }
        // dd(in_array($request->status,['in progress']));
        // dd($task->load('dependencies'));

            if (in_array($request->status, ['in progress', 'completed'])) {
                if ($task->dependencies && !$task->dependencies->isEmpty()) {
                    $allDependenciesCompleted = $this->checkDependenciesCompleted($task);
                    if (!$allDependenciesCompleted) {
                        return response()->json([
                            'message' => 'Task cannot move to "in progress" or "completed" until all dependencies are completed.',
                            'status' => false,
                        ], 400);
                    }
                }
            }
            if ($task->subtasks->isNotEmpty()) {
                if ($request->status === 'completed') {
                    $allSubtasksCompleted = $this->checkSubtasksCompleted($task);
                    if (!$allSubtasksCompleted) {
                        return response()->json([
                            'message' => 'Task cannot be marked as "completed" until all subtasks are marked as "completed."',
                            'status' => false,
                        ], 400);
                    }
            }

        if($request->status != null)
        {
            if($request->status != $task->status)
                TaskHistory::create([
                    'task_id'=>$id,
                    'status'=>$request->status,
                    'updated_by'=> Auth::id(),
                ]);
            }
        }

        if($task->assigned_user_id != $request->assigned_user_id)
        {
            if($request->assigned_user_id)
            {
                $user = User::find($request->assigned_user_id);
                if(!$user)
                {
                    return response()->json([
                        'message' => 'User not found',
                        'status' => false,
                    ],404);    
                }
                $user->sendTaskAssignedNotification($task);
            }
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $task->update([
            "name" => $request->name,
            "description" => $request->description ?? $task->description,
            "status" => $request->status ?? $task->status,
            'priority' => $request->priority ?? $task->priority,
            'project_id' => $request->project_id ?? $task->project_id,
            'parent_task_id' => $request->parent_task_id ?? $task->parent_task_id,
            'assigned_user_id' => $request->assigned_user_id ?? $task->assigned_user_id,
            "start_date" => $startDate ?? $task->start_date,
            "end_date" => $endDate ?? $task->end_date,
        ]);
        // dd(true);

        return response()->json([
            'message' => 'Task successfully updated',
            'status' => true,
            'data' =>new TaskResource($task),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
    
        $task->delete();
    
        return response()->json([
            'message' => 'Task and its related data successfully deleted.',
            'status' => true,
        ], 200);
    }

    public function assignTask(Request $request, $taskId)
    {
        $task = Task::find($taskId);
        if(!$task)
        {
            return response()->json([
                'message' => 'Task not found',
                'status' => false,
            ],404); 
        }

        $user = User::find($request->user_id);
        if(!$user)
        {
            return response()->json([
                'message' => 'User not found',
                'status' => false,
            ],404);    
        }

        $task->assigned_user_id = $request->user_id;
        $task->save();

        $user->sendTaskAssignedNotification($task);

        return response()->json([
            'message' => 'Task assigned and notification sent.',
            'status' => true,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'name' => 'string|nullable',
            'status' => 'string|in:pending,in progress,completed|nullable',
            'assigned_user_id' => 'integer|exists:users,id|nullable',
        ]);

        $tasks = Task::query();

        if ($request->filled('name')) {
            $tasks->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('status')) {
            $tasks->where('status', $request->status);
        }

        if ($request->filled('assigned_user_id')) {
            $tasks->where('assigned_user_id', $request->assigned_user_id);
        }

        $tasks = $tasks->with(['project', 'assignedUser'])->get();

        return response()->json([
            'message' => 'Search results retrieved successfully.',
            'data' => TaskResource::collection($tasks),
        ], 200);
    }

    private function checkDependenciesCompleted(Task $task): bool
    {
        foreach ($task->dependencies as $dependency) {
            if ($dependency->status !== 'completed') {
                return false;
            }
        }

        return true;
    }

    private function checkSubtasksCompleted(Task $task): bool
    {
        foreach ($task->subtasks as $subtask) {
            if ($subtask->status !== 'completed') {
                return false;
            }
        }
        return true;
    }
}
