<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => "",
            'description' => "nullable",
            'status' => "string|in:pending,in progress,completed|nullable",
            'priority' => "numeric|nullable",
            'project_id' => "numeric|nullable",
            'parent_task_id' => "numeric|nullable",
            'assigned_user_id' => "numeric|nullable",
            'start_date' => "date|nullable",
            'end_date' => "date|nullable",
        ];
    }
}
