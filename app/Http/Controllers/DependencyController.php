<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => "required|numeric",
            'dependency_id' => "required|numeric",
        ]);

        $task = Task::find($request->task_id);
        $dependency = Task::find($request->dependency_id);

        if(!$task)
        {
            return response()->json([
                'message' => 'task not found',
                'status' => false,
            ],404);
        }

        if(!$dependency)
        {
            return response()->json([
                'message' => 'dependant task not found',
                'status' => false,
            ],404);
        }

        if ($task->hasCircularDependency($dependency->id)) {
            return response()->json([
                'message' => 'Circular dependency detected. Cannot add this dependency.',
                'status' => false,
            ], 400);
        }

        $task->dependencies()->attach($dependency->id);
    

        // $dependency = TaskDependency::create([
        //     'task_id' => $request->task_id,
        //     'dependency_id' => $request->dependency_id,
        // ]);
        
        if($dependency)
        {
            return response()->json([
                'message' => 'dependency successfully created',
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

    public function destroy(Request $request)
    {
        $request->validate([
            'task_id' => "required|numeric",
            'dependency_id' => "required|numeric",
        ]);

        $dependency = TaskDependency::where('task_id', $request->task_id)
        ->where('dependency_id', $request->dependency_id)->first();

        if($dependency)
        {
            $dependency->delete();
            return response()->json([
                'message' => 'dependency successfully deleted',
                'status' => true,
            ],200); 
        }
        else{
            return response()->json([
                'message' => 'dependency not found',
                'status' => false,
            ],404);
        }
    }
}
