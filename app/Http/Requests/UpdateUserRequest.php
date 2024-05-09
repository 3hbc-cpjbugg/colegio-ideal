<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends StoreUserRequest
{
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
        $parentRules = parent::rules();

        unset($parentRules['role']);

        unset($parentRules['program_id']);

        unset($parentRules['email']);

        return  array_merge($parentRules, [
            'email' => [
                'required',
                'email',
                'max:255',
                'string',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')->id),
            ]
        ]);
    }
}
