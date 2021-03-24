<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('quiz');
            $table->bigInteger('test')->nullable();
            $table->string('quizLabel');
            $table->integer('timer')->default(0);
            $table->text('note')->nullable()->default('');
            $table->text('text');
            $table->integer('type');
            $table->string('source');
            $table->integer('state')->default(4);

            $table->foreign('quiz')->references('id')->on('quiz_sessions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('test')->references('id')->on('tests')
                ->onUpdate('cascade')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_sessions');
    }
}
