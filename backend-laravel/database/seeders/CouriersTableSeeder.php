<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CouriersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Courier::factory()->count(10)->create();
    }
}
