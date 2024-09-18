<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make("123456"),
            'role_id' => 1, 
            'phone' => '0234282934',
            'img' => '',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => Hash::make("123456"),
            'role_id' => 2, // adjust the role_id according to your roles table
            'phone' => '0234282934',
            'img' => '',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
