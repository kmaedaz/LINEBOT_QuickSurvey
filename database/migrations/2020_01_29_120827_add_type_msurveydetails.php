<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeMsurveydetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveydetails', function (Blueprint $table) {
            $table->tinyInteger('type')->default(0)->after('eval')->comment('形式');  //カラム追加
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
           $table->dropColumn('type');  //カラムの削除
            //
        });
    }
}

