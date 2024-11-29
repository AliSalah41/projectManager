<?php

namespace App\Http\Controllers;

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

        $dependency = TaskDependency::create([
            'task_id' => $request->task_id,
            'dependency_id' => $request->dependency_id,
        ]);
        
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
