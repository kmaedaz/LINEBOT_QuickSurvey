<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumquesLinestatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('linestates', function (Blueprint $table) {
     $table->integer('numques')->default(0)->after('qid')->comment('設問数');

            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('linestates', function (Blueprint $table) {
           $table->dropColumn('numques');
            //
        });
    }
}
