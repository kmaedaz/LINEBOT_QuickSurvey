<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModQnumToMsurveysTable extends Migration
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
		$table->renameColumn('qnum', 'qid')->string('qid')->change();  ;
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
		$table->renameColumn('qid', 'qnum');
            //
        });
    }
}
