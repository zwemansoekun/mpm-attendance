<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelayTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delay_times', function (Blueprint $table) {
            $table->id();
            $table->string('month')->comment = "年月(year/month)";
            $table->integer('am')->comment = "AM遅刻許容時間";
            $table->integer('pm')->comment = "PM遅刻許容時間";
            $table->decimal('money',10, 2)->comment = "JPN/MMK";
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
        Schema::dropIfExists('delay_times');
    }
}
