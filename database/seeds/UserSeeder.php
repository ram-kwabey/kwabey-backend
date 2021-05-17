<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'super',
            'last_name' => 'admin',
            'name' => 'super admin',
            'username' => 'super_admin',
            'email' => 'admin@kwabey.com',
            'password' => Hash::make('123456'),
            'status' => 1,
            'email_verified_at' => Carbon::now(),
       ]);
    }
}
