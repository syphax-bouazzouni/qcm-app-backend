<?php

use illuminate\database\migrations\migration;
use illuminate\database\schema\blueprint;
use illuminate\support\facades\schema;

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
            $table->string('password');
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
