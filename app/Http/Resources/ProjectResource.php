<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "name" => $this->name ?? null,
            "description" => $this->description ?? null,
            "status" => $this->status ?? null,
            "start_date" => $this->start_date ?? null,
            "end_date" => $this->end_date ?? null,
            "tasks" => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
