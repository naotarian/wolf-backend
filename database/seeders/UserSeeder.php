<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 11; $i++) {
            User::create([
                'name' => "test{$i}",
                'email' => "test{$i}@test.com",
                'character_id' => $i < 4 ? $i : floor(($i) / 3),
                'email_verified_at' => Carbon::now(),
                'password' => 'aaaaaaaa'
            ]);
        }
    }
}
