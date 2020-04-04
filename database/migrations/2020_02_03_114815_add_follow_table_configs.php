<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowTableConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->string('follow_message1')->default("")->after('linesecret');
            $table->string('follow_imageurl')->default("")->after('follow_message1');
            $table->string('follow_message2')->default("")->after('follow_imageurl');
			
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
        Schema::table('configs', function (Blueprint $table) {
         $table->dropColumn('follow_message1');
         $table->dropColumn('follow_imageurl');
         $table->dropColumn('follow_message2');
            //
        });
    }
}
