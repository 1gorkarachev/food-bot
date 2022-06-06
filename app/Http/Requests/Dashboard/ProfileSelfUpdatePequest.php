<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProfileSelfUpdatePequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'sometimes|string|nullable',
            'city' => 'sometimes|string|nullable',
            'birth' => 'sometimes|date|nullable',
            'photo' => 'sometimes|image|nullable',
        ];
    }
}
