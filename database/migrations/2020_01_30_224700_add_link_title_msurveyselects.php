<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkTitleMsurveyselects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveyselects', function (Blueprint $table) {
            $table->string('button_title')->default("選択する")->after('message');
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
	         $table->dropColumn('button_title');
            //
        });
    }
}
