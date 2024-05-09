<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Program;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrator = User::create([
            'name' => 'Administrador Enrique',
            'username' => 'admin',
            'email' => 'administrador@mail.com',
            'password' => 'password'
        ]);

        $administrator->assignRole('administrador');

        $profesor = User::create([
            'name' => 'Profesor Julian',
            'username' => 'profe',
            'email' => 'profesor@mail.com',
            'password' => 'password',
            'program_id' => Program::first()->getKey()
        ]);

        $profesor->assignRole('profesor');
    }
}
