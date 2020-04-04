<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvalToLinechatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('linechats', function (Blueprint $table) {
	        $table->integer('eval')->after("results")->comment("評価値");  //カラム追加

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
        Schema::table('linechats', function (Blueprint $table) {
           $table->dropColumn('eval');  //カラムの削除

            //
        });
    }
}


