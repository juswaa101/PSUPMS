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
                'username' => 'admin', 'name' => 'admin', 'email' => 'admin@example.com',
                'password' => Hash::make('password'), 'role' => 'admin',
                'department' => 'IT Administrator'
            ],
        ]);
    }
}
