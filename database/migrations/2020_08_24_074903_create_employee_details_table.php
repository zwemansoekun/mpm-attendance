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
            $table->string('position')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('nrc_no')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('member')->nullable();
            $table->string('child')->nullable();
            $table->string('emg_ph_no')->nullable();
            $table->string('waste_time')->nullable();
            $table->integer('emp_id'); //api_emp_id
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
