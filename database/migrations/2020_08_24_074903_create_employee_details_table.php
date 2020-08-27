<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_details', function (Blueprint $table) {
            $table->id();
            $table->string('pay_month');
            $table->decimal('salary_amount',10, 2);
            $table->integer('trans_money');
            $table->integer('jlpt');
            $table->integer('ssb');
            $table->string('position');
            $table->text('address');
            $table->string('phone_no');
            $table->string('nrc_no');
            $table->string('bank_account');
            $table->string('member');
            $table->string('child');
            $table->string('emg_ph_no');
            $table->string('waste_time');
            $table->integer('employee_id'); //f_key
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
        Schema::dropIfExists('employee_details');
    }
}
