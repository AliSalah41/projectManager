<?php

namespace App\Http\Controllers;

use App\Http\Resources\HistoryResource;
use App\Models\TaskHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function show($id)
    {
        $taskHistory = TaskHistory::with(['task','updatedBy'])->where('task_id',$id)->get();

        if($taskHistory->isNotEmpty())
        {
            return response()->json([
                'status' => true,
                'message' => 'History retrieved successfully',
                'data' =>HistoryResource::collection($taskHistory),
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
}
