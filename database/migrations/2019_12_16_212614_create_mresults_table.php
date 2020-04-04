<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMresultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mresults', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->text('imageurl')->nullable()->comment("画像URL");   /*  画像URL */
			$table->text('message')->nullable()->comment("メッセージ"); /*  メッセージ */
            $table->integer('eval')->default(0)->comment("評価点");     /* 評価点 */


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
        Schema::dropIfExists('mresults');
    }
}
