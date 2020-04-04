<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		/*
        Schema::create('resultitems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surveyid');
            $table->unsignedBigInteger('mresultsid');
            $table->string('title');
            $table->string('imageurl');
            $table->string('message');
            $table->string('link_url');
            $table->timestamps();
        });
		*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resultitems');
    }
}
