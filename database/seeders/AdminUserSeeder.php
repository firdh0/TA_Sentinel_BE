<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin_users')->insert([
            'name' => 'Sanctum Sentinel',
            'email' => 'muhammadfirdho4@gmail.com',
            'password' => bcrypt('@S3nt1N3L')
        ]);
    }
}
