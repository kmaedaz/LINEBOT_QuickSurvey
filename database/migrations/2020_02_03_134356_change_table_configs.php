<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTableConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
			 $table->string('follow_message1')->nullable()->change();
			 $table->string('follow_imageurl1')->nullable()->change();
			 $table->string('follow_message2')->nullable()->change();
			 $table->string('follow_imageurl2')->nullable()->change();
			 $table->string('follow_message3')->nullable()->change();
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
            //
        });
    }
}
