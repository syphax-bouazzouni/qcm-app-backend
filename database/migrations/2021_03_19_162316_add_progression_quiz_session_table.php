<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgressionQuizSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('progression');
            $table->foreign('progression')->references('id')->on('quiz_session_progressions')
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


         Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->dropForeign('quiz_sessions_progression_foreign');
            $table->removeColumn('progression');
        });

    }
}
