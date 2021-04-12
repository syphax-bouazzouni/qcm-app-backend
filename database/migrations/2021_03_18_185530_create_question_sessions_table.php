<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test');
            $table->unsignedBigInteger('question')->nullable()->nullOnDelete();
            $table->text('text');
            $table->text('explication')->nullable()->default('');
            $table->integer('type');
            $table->integer('state')->default(4);
            $table->boolean('isQrocResponded')->default(false);
            $table->timestamps();

            $table->foreign('test')->references('id')->on('test_sessions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('question')->references('id')->on('questions')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_sessions');
    }
}
