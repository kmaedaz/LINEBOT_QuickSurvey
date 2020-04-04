<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTableConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
				$table->renameColumn('follow_imageurl', 'follow_imageurl1');
				$table->renameColumn('follow_message4', 'follow_message3');
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
				$table->renameColumn( 'follow_imageurl1','follow_imageurl');
				$table->renameColumn( 'follow_message3','follow_message4');
            //
        });
    }
}
