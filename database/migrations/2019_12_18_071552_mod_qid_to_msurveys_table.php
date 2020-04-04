<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModQidToMsurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveyselects', function (Blueprint $table) {
            $table->string('qid')->after('surveyid')->change();  //カラム変更
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
        Schema::table('msurveyselects', function (Blueprint $table) {
           $table->integer('qid')->change();  //カラム変更
            //
        });
    }
}

