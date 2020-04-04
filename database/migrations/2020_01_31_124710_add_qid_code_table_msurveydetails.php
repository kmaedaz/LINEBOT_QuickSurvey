<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQidCodeTableMsurveydetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveydetails', function (Blueprint $table) {
            $table->string('qid_code')->after('qid');

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
        Schema::table('msurveydetails', function (Blueprint $table) {
         $table->dropColumn('qid_code');
            //
        });
    }
}

