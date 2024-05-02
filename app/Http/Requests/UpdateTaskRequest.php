<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        if ( ! $this->user()->can( 'update', $task = $this->route('task') )) {
            return false;
        }

        // if any of the task dependencies status are not completed
        // the the main task won't be updated
        if ( $this->filled('status') && $this->status == 'completed'
                && $task->dependencies()->where('status', '!=', 'completed')->count() > 0) {

                return false;
            }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->user()->role == 'user') {
            return ['status' => 'required|in:completed,canceled,pending'];
        }
        
        return [
            'title'         => 'string',
            'description'   => 'string',
            'due_date'      => 'date',
            'status'        => 'in:completed,canceled,pending',
            'assignee'      => 'numeric',
            'dependecies'   => 'array|numeric|nullable',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response(['error' => $validator->errors()], 422));
    }
}
