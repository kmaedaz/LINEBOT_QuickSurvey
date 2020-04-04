<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTresultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tresults', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('surveyid')->unsigned()->comment("質問NO");
			$table->bigInteger('qid')->unsigned()->comment("設問NO");
			$table->string('userid')->comment("USERID");
			$table->string('results')->comment("回答結果 (複数の場合カンマ区切り)O");


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tresults');
    }
}
