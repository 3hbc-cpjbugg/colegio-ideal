<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RetrieveUserRequest extends FormRequest
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
            'role' => [
                'nullable',
                Rule::exists('roles', 'name')
            ],
            'per_page' => [
                'nullable',
                'numeric',
                'min:0',
                'max:500'
            ],
            'paginate' => [
                'nullable'
            ],
        ];

    }
}
