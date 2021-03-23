<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderQuizTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_test', function (Blueprint $table) {
            $table->integer('order')->default(0);
        });
        $quizzes = \App\Models\Quiz::with('tests')->get();
        foreach ($quizzes as $q){
            foreach ($q->tests as $k=>$t){
                $t->pivot->order = $k+1;
                $t->pivot->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_test', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
