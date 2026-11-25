<?php

namespace Database\Seeders;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear rol administrador
        $administrator = Role::firstOrCreate(
            ['name' => 'administrador'],
            [
                'description' => 'Acceso de administración del sistema.'
            ]
        );

        // Crear usuario administrador
        User::updateOrCreate(
            ['email' => 'admin@sanjuan.com'],
            [
                'rol_id' => $administrator->id,
                'name' => 'Administrador',
                'dni' => '00000001',
                'phone' => '999999999',
                'tuition' => null,
                'password' => bcrypt('password'),
            ]
        );
    }
}
