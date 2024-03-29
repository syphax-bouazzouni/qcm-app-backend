<?php

namespace Database\Seeders;

use App\Http\Controllers\YearsController;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UsersTableSeeder::class);
        // $this->call(ModuleTableSeeder::class);
        // $this->call(QuizTableSeeder::class);
        // $this->call(ExamQuizTableSeeder::class);
        // $this->call(OfferTableSeeder::class);
        $this->call(YearTableSeeder::class);
    }
}
