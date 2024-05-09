<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'administrador', 'guard_name' => 'web']);
        Role::create(['name' => 'profesor', 'guard_name' => 'web']);
    }
}
