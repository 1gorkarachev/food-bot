<?php

namespace App\Imports\Users;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithValidation, WithHeadingRow
{
    public function model(array $row)
    {
        $user = new User([
            'name'     => $row['name'],
            'surname'  => $row['surname'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']),
            'birth'    => Carbon::parse($row['birthday'])->format('Y-m-d'),
        ]);
        $user->save();
        $user->assignRole('user');

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:6'],
            'birthday' => ['sometimes', 'date'],
        ];
    }
}
