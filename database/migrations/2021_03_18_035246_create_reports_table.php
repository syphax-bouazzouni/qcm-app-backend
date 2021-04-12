<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user')->nullOnDelete();
            $table->integer('subject');
            $table->text('text')->nullable();
            $table->unsignedBigInteger('test')->nullOnDelete();
            $table->timestamps();

            $table->foreign('user')->references('id')->on('users')
                ->onUpdate('cascade');
            $table->foreign('test')->references('id')->on('tests')
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
        Schema::dropIfExists('reports');
    }
}
