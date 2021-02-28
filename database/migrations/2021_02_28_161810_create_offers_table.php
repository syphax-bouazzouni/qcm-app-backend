<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->integer('price')->default(0);
            $table->string('image')->nullable();
            $table->integer('state');
            $table->timestamps();

            $table->foreign('image')->references('title')->on('images')
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
        Schema::dropIfExists('offers');
    }
}
