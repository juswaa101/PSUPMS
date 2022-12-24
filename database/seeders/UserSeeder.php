<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'username' => 'IT Admin', 'name' => 'IT Administrator', 'email' => 'it_admin@psu.edu.ph',
                'password' => Hash::make('psu_golden_lion'), 'role' => 'admin',
                'department' => 'IT Administrator'
            ],
        ]);
    }
}
