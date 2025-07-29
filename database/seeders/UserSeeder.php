<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role_id' => 1,
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'avatar' => 'default.png',
            'password' => 'password',
        ]);
    }
}
