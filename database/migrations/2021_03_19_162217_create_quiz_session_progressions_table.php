<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizSessionProgressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_session_progressions', function (Blueprint $table) {
            $table->id();
            $table->float('success');
            $table->float('error');
            $table->float('notRespond');
            $table->float('rest');
            $table->bigInteger('quiz');
            $table->timestamps();

            $table->foreign('quiz')->references('id')->on('quiz_sessions')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_session_progressions');
    }
}
