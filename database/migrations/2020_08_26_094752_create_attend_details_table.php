<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attend_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('emp_no');
            $table->decimal('total_hours',8,2);
            $table->time('am1');
            $table->time('am2');
            $table->time('pm1');
            $table->time('pm2');
            $table->smallInteger('am_leave');
            $table->smallInteger('pm_leave');
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
        Schema::dropIfExists('attend_details');
    }
}
