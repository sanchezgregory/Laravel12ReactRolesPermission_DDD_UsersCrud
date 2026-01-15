<?php

namespace Database\Seeders;

use App\Models\MediatorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MediatorUsersSeeder extends Seeder
{
    public function run(): void
    {

        $role = Role::firstOrCreate(['name' => 'mediator']);
        $url = 'https://calendly.com/mcgregox/30min';

        // Fixed Mediator User
        $user = User::firstOrCreate(
            ['email' => 'mediator@gmail.com'],
            [
                'name' => 'Mediator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($role);

        MediatorProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'session_price_minor' => 15000,
                'currency' => 'USD', // Fixed currency for testing
                'calendly_url' => $url,
                'headline' => 'Expert Mediator',
                'bio' => 'Senior mediator for platform testing and demonstration.',
            ]
        );

        User::factory()
            ->count(20)
            ->create([
                'password' => Hash::make('password'),
            ])
            ->each(function (User $user) use ($role, $url) {
                $user->assignRole($role);

                MediatorProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'session_price_minor' => random_int(5000, 25000),
                        'currency' => 'USD',
                        'calendly_url' => $url,
                        'headline' => 'Mediación en conflictos personales y business',
                        'bio' => 'Mediador con experiencia. Enfoque práctico y orientado a acuerdos.',
                    ]
                );
            });
    }
}
