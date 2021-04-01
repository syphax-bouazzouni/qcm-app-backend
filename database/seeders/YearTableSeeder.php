<?php

namespace Database\Seeders;

use App\Models\Year;
use Illuminate\Database\Seeder;

class YearTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Year::create(['title' => '1ere année' ,'order' => 1]);
        Year::create(['title' => '2eme année' ,'order' => 2]);
        Year::create(['title' => '3eme année' , 'order' => 3]);
        Year::create(['title' => '4eme année' , 'order' => 4]);
        Year::create(['title' => '5eme année', 'order' => 5]);
        Year::create(['title' => '6eme année', 'order' => 6]);
    }
}
