<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\User;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $_admin_role = Role::create(
            [
                'name' => 'admin',
                'guard_name' => 'web'
            ]
        );

        $_user = User::where( 'id', 1 )->first();
        $_user->assignRole([ $_admin_role ]);
    }
}
