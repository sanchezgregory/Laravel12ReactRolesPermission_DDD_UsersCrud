<?php

namespace Database\Seeders;

use App\Models\MediatorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MediatorUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Asegura que exista el role
        $role = Role::firstOrCreate(['name' => 'mediator']);
        $url = 'https://calendly.com/mcgregox/30min';
        // Crea 20 mediadores con perfiles
        User::factory()
            ->count(20)
            ->create([
                // password conocida para pruebas
                'password' => Hash::make('password'),
            ])
            ->each(function (User $user) use ($role, $url) {
                $user->assignRole($role);

                // Perfil del mediador
                MediatorProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'session_price_minor' => random_int(5000, 25000), // 50€ a 250€
                        'currency' => 'EUR',
                        'calendly_url' => $url,
                        'headline' => 'Mediación en conflictos personales y business',
                        'bio' => 'Mediador con experiencia. Enfoque práctico y orientado a acuerdos.',
                    ]
                );
            });
    }
}
