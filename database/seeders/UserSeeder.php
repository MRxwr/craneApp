<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
            'name' => 'Admin',
            'email' => 'admin@createkw.com',
            'email_verified_at' => now(),
            'password' => bcrypt('createkw@786'),
            'role_id'=>1,
            'is_active' => 1
        ]);
    }
}
