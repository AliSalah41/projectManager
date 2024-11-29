<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'name' => "required|string",
            'description' => "string|nullable",
            'status' => "required|string|in:pending,in progress else completed",
            'start_date' => "required|date",
            'end_date' => "required|date|after_or_equal:start_date",
        ];
    }

    public function wantsJson()
    {
        return true;
    }
}
