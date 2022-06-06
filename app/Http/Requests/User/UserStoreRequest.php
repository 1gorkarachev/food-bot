<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'surname' => 'required|string',
            'birth' => 'nullable|date',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->id)],
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,id'
        ];
    }
}
