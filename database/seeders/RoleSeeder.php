<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Buat dan simpan role
        $roles = ['Admin', 'Headbar', 'Headkitchen', 'Bar', 'Kitchen'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Buat User Contoh
        $user1 = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('Admin123')
        ]);

        // Berikan role Admin ke user
        $user1->assignRole('Admin');
    }
}
