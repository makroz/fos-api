<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        if (DB::table('levels')->get()->count() == 0) {
            DB::table('levels')->insert([
                ['name' => 'Invited'],
                ['name' => 'Prospect'],
                ['name' => 'Member'],
            ]);
        }

        if (DB::table('challenges')->get()->count() == 0) {
            DB::table('challenges')->insert([
                [
                    'name' => 'Challenge 1',
                    'description' => 'Challenge 1',
                    'repeat' => 5,
                    'position' => 1,
                    'points' => 100,
                    'level_id' => 1,
                ],
            ]);
        }

        if (DB::table('abilities')->get()->count() == 0) {
            DB::table('abilities')->insert([
                ['name' => 'home_adm', 'description' => 'Inicio Admin',],
                ['name' => 'home_ins', 'description' => 'Inicio Instructor',],
                ['name' => 'home', 'description' => 'Inicio',],
                ['name' => 'users', 'description' => 'Usuarios',],
                ['name' => 'settings', 'description' => 'Configuraciones',],
                ['name' => 'levels', 'description' => 'Niveles',],
                ['name' => 'challenges', 'description' => 'Challenges',],
                ['name' => 'tasks', 'description' => 'Tareas',],
                ['name' => 'roles', 'description' => 'Roles',],
                ['name' => 'abilities', 'description' => 'Permisos',],
            ]);
        }

        if (DB::table('roles')->get()->count() == 0) {
            DB::table('roles')->insert([
                ['name' => 'adm', 'description' => 'Administrador', 'abilities' => 'home:CRUD|users:CRUD|settings:CRUD|levels:CRUD|challenges:CRUD|tasks:CRUD|roles:CRUD|abilities:CRUD'],
            ]);
        }
    }
}
