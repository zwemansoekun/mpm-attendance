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
            $table->integer('income');
            // $table->decimal('income',10, 2);
            $table->integer('trans_money')->nullable();
            $table->integer('jlpt')->nullable();
            $table->integer('bonus')->nullable();
            $table->integer('income_tax');
            $table->integer('ssb');
            $table->integer('total_salary');
            // $table->decimal('ssb',10, 2);
            $table->integer('leave_late')->nullable();
            // $table->decimal('leave_late',10, 2);
            $table->integer('payment_amount');
            // $table->decimal('payment_amount',10, 2);
            $table->integer('employee_id'); // f_key
            $table->integer('ssb_id'); //f_key //check pay pr
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
        Schema::dropIfExists('salaries');
    }
}
