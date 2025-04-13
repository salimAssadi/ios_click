<?php

namespace Database\Seeders;

use App\Models\ProcedureTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {   
        $superAdminRoleData =  [
            'name' => 'super admin',
            'parent_id' => 0,
        ];
        // $systemSuperAdminRole = Role::create($superAdminRoleData);
        $superAdminData =     [
            'first_name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('123456'),
            'type' => 'super admin',
            'lang' => 'english',
            'email_verified_at' => now(),
            'profile' => 'avatar.png',
        ];
        $systemSuperAdmin = User::create($superAdminData);
        // $systemSuperAdmin->assignRole($systemSuperAdminRole);
    }
}
