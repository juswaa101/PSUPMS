<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
                'uuid' => (string) Str::uuid(),
                'username' => 'IT Admin', 'name' => 'IT Administrator', 'email' => 'it_admin@psu.edu.ph',
                'password' => Hash::make('psu_golden_lion'), 'role' => 'admin',
                'department' => 'IT Administrator'
            ],
        ]);
    }
}
