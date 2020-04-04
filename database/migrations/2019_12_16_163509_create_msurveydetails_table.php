<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsurveydetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msurveydetails', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('surveyid')->unsigned()->comment("surveyd_ID");
			$table->text('title')->nullable()->comment("タイトル");     /*  タイトル */
			$table->text('imageurl')->nullable()->comment("画像URL");   /*  画像URL */
			$table->text('message')->nullable()->comment("メッセージ"); /*  メッセージ */
            $table->integer('qnum')->comment(" 設問NO");                /* 設問NO */
            $table->integer('disnum')->comment(" 表示順");              /* 表示順 */
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
        Schema::dropIfExists('msurveydetails');
    }
}
