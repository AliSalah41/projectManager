<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'task' =>new TaskResource($this->whenLoaded('task')),
            'updated_by' =>new UserResource($this->whenLoaded('updatedBy')),
            'status' => $this->status,
        ];
    }
}
