<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminType = DB::table('user_types')->where('role', 'Admin')->first();

        DB::table('users')->updateOrInsert([
            'id' => (string) Str::uuid(),
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'user_type_id' => $adminType->id,
            'password' => bcrypt('123456'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null,
        ]);
    }
}
