<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Armazém 1', 'armazem'],
            ['Armazém 2', 'armazem'],
            ['Yard 1',    'yard'],
            ['Yard 2',    'yard'],
        ] as [$nome, $tipo]) {
            Location::firstOrCreate(['nome' => $nome], ['tipo' => $tipo]);
        }
    }
}
