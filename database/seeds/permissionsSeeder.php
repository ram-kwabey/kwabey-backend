<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class permissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //listing permissions
        Permission::create(['name' => 'list_dashboard']);
        Permission::create(['name' => 'list_appointments']);
        Permission::create(['name' => 'list_users']);
        Permission::create(['name' => 'list_settings']);

        // create permissions
        Permission::create(['name' => 'create_user']);
        
        $role = Role::where('name','Admin')->first();
        $role->syncPermissions(Permission::all());


    }
}
