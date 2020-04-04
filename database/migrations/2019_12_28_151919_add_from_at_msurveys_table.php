<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromAtMsurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveys', function (Blueprint $table) {
			$table->dateTime('to_at')->default('2030-12-31 23:59:00')->after('inactive');
			$table->dateTime('from_at')->default('2019-12-01 00:00:00')->after('inactive');
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
        Schema::table('msurveys', function (Blueprint $table) {
           $table->dropColumn('from_at');  //ƒJƒ‰ƒ€‚Ìíœ
           $table->dropColumn('to_at');  //ƒJƒ‰ƒ€‚Ìíœ
            //
        });
    }
}
