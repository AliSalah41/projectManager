<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return[
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'project' => $this->project_id,
            'project' => $this->project->name,
            'parent_task_id' => $this->parent_task_id ?? null,
            'parent_task' => $this->subtasks()->name ?? null,
            'assigned_user_id' => $this->assigned_user_id,
            'assigned_user' => $this->assignedUser->name ?? null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'subtasks' => TaskResource::collection($this->whenLoaded('subtasks')),       
            'parentTask' => new TaskResource($this->whenLoaded('parentTask')),      
            'dependencies' =>TaskResource::collection($this->whenLoaded('dependencies'))
        ];
    }
}
