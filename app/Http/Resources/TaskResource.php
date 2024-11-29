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
            'project_id' => $this->project_id,
            'parent_task_id' => $this->parent_task_id,
            'assigned_user_id' => $this->assigned_user_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }
}
