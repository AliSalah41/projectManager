<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $dateRange = $request->query('date_range');

        $projects = Project::query()
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($dateRange, function ($query, $dateRange) {
                $dates = explode(',', $dateRange);
                if (count($dates) === 2) {
                    $query->whereBetween('start_date', [$dates[0], $dates[1]]);
                }
            })
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Projects retrieved successfully',
            'data' => ProjectResource::collection($projects),
        ]);
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
    public function store(ProjectRequest $request)
    {
        $startDate =  Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate =  Carbon::parse($request->end_date)->format('Y-m-d');
        $project = Project::create([
            "name" => $request->name,
            "description" => $request->description ?? null,
            "status" => $request->status,
            "start_date" => $startDate,
            "end_date" => $endDate,
        ]);

        if($project)
        {
            return response()->json([
                'message' => 'project successfully created',
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
        $project = Project::with('tasks')->where('id', $id)->first();
        if($project)
        {
            return response()->json([
                'status' => true,
                'message' => 'Project retrieved successfully',
                'data' =>new ProjectResource($project),
            ]);
        }
        else {
            return response()->json([
                'message' => 'Project not found',
                'status' => false,
            ],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $startDate =  Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate =  Carbon::parse($request->end_date)->format('Y-m-d');
        $project = Project::create([
            "name" => $request->name,
            "description" => $request->description ?? null,
            "status" => $request->status,
            "start_date" => $startDate,
            "end_date" => $endDate,
        ]);

        if($project)
        {
            return response()->json([
                'message' => 'project successfully created',
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
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
