<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Gregory Sanchez',
            'email' => 'gregorysanchez@whap.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('admin');

        $user = User::create([
            'name' => 'User',
            'email' => 'user@whap.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('user');
    }
}
