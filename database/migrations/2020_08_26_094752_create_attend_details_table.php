<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->decimal('total_hours',8,2)->nullable();
            $table->time('am1')->nullable();
            $table->time('am2')->nullable();
            $table->time('pm1')->nullable();
            $table->time('pm2')->nullable();
            $table->smallInteger('am_leave')->nullable();
            $table->smallInteger('pm_leave')->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            // $table->timestamps();
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
