<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function validationData()
    {
        $data = $this->all();

        if (!Arr::get($data, 'password')) {
            $data = Arr::except($data, 'password');
        }

        return $data;
    }

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
            'birth_text' => 'nullable|string',
            'role' => 'required|exists:roles,id',
            'password' => 'sometimes|required|min:6',
        ];
    }
}
