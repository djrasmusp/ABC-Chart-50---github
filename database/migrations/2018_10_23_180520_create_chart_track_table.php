<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartTrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_track', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('chart_id');
            $table->integer('position');
            $table->unsignedInteger('track_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chart_track');
    }
}
