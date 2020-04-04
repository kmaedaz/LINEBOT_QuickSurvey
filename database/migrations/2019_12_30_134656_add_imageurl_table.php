<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageurlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msurveys', function (Blueprint $table) {
             $table->string('imageurl')->default("")->after("title") ;
 
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
            $table->dropColumn('imageurl');  
 
            //
        });
    }
}


 