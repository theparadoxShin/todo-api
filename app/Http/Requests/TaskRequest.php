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
        // Check if the user is authenticated
        if ($this->user()) {
            // Check if the user has permission to perform this action
            return $this->user()->can('create', $this->route('task'));
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,completed',
            'priority' => 'integer|between:0,2',
            'is_completed' => 'boolean',
            'due_date' => 'nullable|date',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'status.required' => 'The status is required.',
            'status.in' => 'The selected status is invalid.',
            'priority.integer' => 'The priority must be an integer.',
            'priority.between' => 'The priority must be between 0 and 2.',
            'is_completed.boolean' => 'The completed field must be true or false.',
            'due_date.date' => 'The due date must be a valid date.',
        ];
    }
}
