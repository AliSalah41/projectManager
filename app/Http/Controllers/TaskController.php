<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskHistory;
use Carbon\Carbon;
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
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
                'status' => false,
            ], 404);
        }

        if($request->status != null && $request->status != $task->status)
        {
            TaskHistory::create([
                'task_id'=>$id,
                'status'=>$request->status,
                'updated_by'=> Auth::id(),
            ]);
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

        return response()->json([
            'message' => 'Task successfully updated',
            'status' => true,
            'data' =>new TaskResource($task),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
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
}
