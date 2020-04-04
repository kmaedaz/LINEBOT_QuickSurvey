<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLinkUrlMresultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mresults', function (Blueprint $table) {
		 $table->renameColumn('link_url', 'title');
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
        Schema::table('mresults', function (Blueprint $table) {
		 $table->renameColumn( 'title','link_url');
        });
    }
}
