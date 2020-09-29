<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('money',10, 2)->comment = "為替レートデフォルト値";
            $table->integer('am')->comment = "AM遅刻許容時間デフォルト値";
            $table->integer('pm')->comment = "PM遅刻許容時間デフォルト値";
            $table->string('create_month')->comment = "year/month";
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
        Schema::dropIfExists('settings');
    }
}
