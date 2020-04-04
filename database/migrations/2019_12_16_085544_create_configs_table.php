<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('linetoken')->nullable()->comment(" LINE TOKEN");     /*  LINE TOKEN */
            $table->string('linesecret')->nullable()->comment(" LINE SECRET");  /*  LINE SECRET */


            $table->integer('dispnum')->comment(" PC 表示件数");       /* PC 表示件数 */
            $table->integer('dispnumsp')->comment(" Mobile 表示件数");     /*  Mobile 表示件数 */


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
        Schema::dropIfExists('configs');
    }
}
