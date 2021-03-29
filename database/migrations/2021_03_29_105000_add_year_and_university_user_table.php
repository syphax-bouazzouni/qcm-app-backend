<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearAndUniversityUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::table('users', function (blueprint $table) {
            $table->integer('year')->nullable()->default(1);
            $table->string('university')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        schema::table('users', function (blueprint $table) {
            $table->dropColumn('year');
            $table->dropColumn('university');
        });
    }
}
