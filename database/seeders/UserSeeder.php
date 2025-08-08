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
        $user = User::create([
            'role_id' => 1,
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'avatar' => 'default.png',
            'password' => 'password',
        ]);

        $user->admin()->create([
            'employee_id' => 'EMP001',
            'full_name' => 'Administrator',
            'phone_number' => '1234567890',
            'address' => 'Jalan Admin No. 1',
        ]);
    }
}
