<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        for ($number = 1; $number <= 60; $number++) {
            Table::firstOrCreate(
                ['number'   => $number],
                ['capacity' => 4, 'status' => 'free']
            );
        }
    }
}
