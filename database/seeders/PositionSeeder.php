<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => '村人', 'is_villager' => 1],
            ['name' => '人狼', 'is_villager' => 0],
            ['name' => '占い師', 'is_villager' => 1],
            ['name' => '霊能者', 'is_villager' => 1],
            ['name' => '狩人', 'is_villager' => 1],
            ['name' => '狂人', 'is_villager' => 1],
        ];
        foreach ($data as $position) {
            Position::create($position);
        }
    }
}
