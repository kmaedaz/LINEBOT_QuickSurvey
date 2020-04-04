<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModSurveyidToLinestatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('linestates', function (Blueprint $table) {
			$table->integer('state')->after('id');
            $table->string('surveyid')->change();  //カラム変更
            $table->string('qid')->change();  //カラム変更
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
            //
        });
    }
}
