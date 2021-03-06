<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerIDToTresultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tresults', function (Blueprint $table) {
	        $table->string('answerid')->after("id")->comment("回答NO");  //回答

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
        Schema::table('tresults', function (Blueprint $table) {
          $table->dropColumn('answerid');  //カラムの削除

            //
        });
    }
}

