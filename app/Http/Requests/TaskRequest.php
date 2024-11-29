<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'name' => "required",
            'description' => "string|nullable",
            'status' => "required|string|in:pending,in progress else completed",
            'priority' => "numeric|nullable",
            'project_id' => "require|numeric",
            'parent_task_id' => "numeric|nullable",
            'assigned_user_id' => "numeric|nullable",
            'start_date' => "date|nullable",
            'end_date' => "date|nullable",
        ];
    }
}
