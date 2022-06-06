<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
            ],
            [
                'name' => 'manager',
            ],
            [
                'name' => 'user',
            ],
        ];

        $roleClass = config('permission.models.role');

        foreach ($roles as $role) {
            $role = $roleClass::firstOrNew(['name' => $role['name']]);

            if ($role->isDirty()) {
                $role->save();
            }
        }
    }
}
