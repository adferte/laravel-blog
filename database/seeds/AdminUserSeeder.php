<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name' => config('app.admin_username'),
            'password' => Hash::make(config('app.admin_password')),
            'email' => config('app.admin_email'),
        ]);
    }
}
