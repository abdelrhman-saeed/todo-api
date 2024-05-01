<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Task::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'         => 'required|alpha_num',
            'description'   => 'required',
            'due_date'      => 'required|date',
            'status'        => 'required|in:completed,canceled,pending',
            'assignee'      => 'required|numeric',
            'task_id'       => 'nullable|numeric'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response(['error' => $validator->errors()], 422));
    }
}
