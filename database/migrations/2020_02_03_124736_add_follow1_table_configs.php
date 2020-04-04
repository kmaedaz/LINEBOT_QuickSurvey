<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollow1TableConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            //
            $table->string('follow_message3')->default("")->after('follow_message2');
            $table->string('follow_imageur2')->default("")->after('follow_message3');
            $table->string('follow_message4')->default("")->after('follow_imageur2');
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
         $table->dropColumn('follow_message3');
         $table->dropColumn('follow_imageur2');
         $table->dropColumn('follow_message4');
        });
    }
}

