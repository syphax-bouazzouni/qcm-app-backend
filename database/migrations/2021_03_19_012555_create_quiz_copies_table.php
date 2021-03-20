<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizCopiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_copies', function (Blueprint $table) {
            $table->id();
            $table->string('quiz_id');
            $table->string('label');
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
        Schema::dropIfExists('quiz_copies');
    }
}
