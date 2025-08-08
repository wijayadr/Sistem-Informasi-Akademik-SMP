<?php

namespace Database\Seeders;

use App\Models\User\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
        ]);

        Role::create([
            'name' => 'Student',
            'slug' => 'student',
        ]);

        Role::create([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);
    }
}
