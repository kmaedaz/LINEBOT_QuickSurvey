<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModSurveyidToMsurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveydetails', function (Blueprint $table) {
            //
            $table->string('surveyid')->change();  //カラム変更

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('msurveydetails', function (Blueprint $table) {
           $table->integer('surveyid')->change();  //カラム変更
            //
        });
    }
}
