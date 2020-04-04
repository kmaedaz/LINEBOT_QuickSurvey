<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurveyidToMsurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveys', function (Blueprint $table) {
            //
            $table->string('surveyid')->unique()->after('id')->comment("surveyid");  //�J�����ǉ�
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('msurveys', function (Blueprint $table) {
           $table->dropColumn('surveyid');  //�J�����̍폜
            //
        });
    }
}
