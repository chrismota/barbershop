<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'client',
        ];
        foreach ($roles as $role) {
            DB::table('user_types')->updateOrInsert([
                'id' => (string) Str::uuid(),
                'role' => $role,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ]);
        }
    }
}
