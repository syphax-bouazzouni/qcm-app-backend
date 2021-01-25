<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class createuserstable extends migration
{
    /**
     * run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::create('users', function (blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable()->default(' ');
            $table->remembertoken();
            $table->timestamps();
        });
    }

    /**
     * reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        schema::dropifexists('users');
    }
}
