<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(\Database\Seeders\RolesSeeder::class);
        $this->call(\Database\Seeders\AdminSeeder::class);
    }
}
