<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateOrderQuizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $quizzes = \App\Models\Quiz::where('isExam' , true)->get();

        foreach ($quizzes as $k => $quiz){
            $quiz->update(['order' => $k +1]);
            $quiz->save();
        }

        $quizzes = \App\Models\Quiz::where('isExam' , false)->get();

        foreach ($quizzes as $k => $quiz){
            $quiz->update(['order' => $k +1]);
            $quiz->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('quizzes')->update(['order'=> 0]);
    }
}
