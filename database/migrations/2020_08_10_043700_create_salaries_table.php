<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->string('pay_month');
            $table->decimal('income',10, 2);
            $table->integer('trans_money');
            $table->integer('jlpt');
            $table->integer('bonus');
            $table->integer('income_tax');
            $table->decimal('ssb',10, 2);
            $table->decimal('leave_late',10, 2);
            $table->decimal('payment_amount',10, 2);
            $table->integer('employee_id'); // f_key
            $table->integer('ssb_id'); //f_key //check pay pr
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
        Schema::dropIfExists('salaries');
    }
}
