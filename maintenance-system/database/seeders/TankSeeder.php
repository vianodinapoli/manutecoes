<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
{
    \App\Models\Tank::updateOrCreate(['nome' => 'Tanque da Fem'], ['capacidade' => 43251, 'stock_atual' => 0]);
    \App\Models\Tank::updateOrCreate(['nome' => 'Bymoze'], ['capacidade' => 20000, 'stock_atual' => 0]);
    \App\Models\Tank::updateOrCreate(['nome' => 'Bomba Móvel'], ['capacidade' => 1000, 'stock_atual' => 0]);
    \App\Models\Tank::updateOrCreate(['nome' => 'Nitro'], ['capacidade' => 24799, 'stock_atual' => 0]);
}
}
