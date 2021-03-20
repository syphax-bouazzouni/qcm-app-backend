<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropositionStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposition_states', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('question');
            $table->string('proposition');
            $table->boolean('isResponse')->default(false);
            $table->boolean('propositionsState')->default(false);
            $table->timestamps();

            $table->foreign('question')->references('id')->on('question_sessions')
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
        Schema::dropIfExists('proposition_states');
    }
}
