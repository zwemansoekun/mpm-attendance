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
            // $table->increments('id');
            $table->string('pay_month')->comment = "給与年月(year/month)";
            $table->decimal('salary_amount',10, 2)->comment = "基本給";
            $table->integer('trans_money')->comment = "通勤交通費(片道/回)";
            $table->integer('jlpt')->comment = "JLPT";
            $table->integer('ssb')->comment = "SSB負担割合(%)";
            $table->string('position')->nullable()->comment = "ポジション";
            $table->text('address')->nullable()->comment = "住所";
            $table->string('phone_no')->nullable()->comment = "電話番号";
            $table->string('nrc_no')->nullable()->comment = "身分証番号";
            $table->string('bank_account')->nullable()->comment = "給与振込先銀行口座";
            $table->string('member')->nullable()->comment = "家族構成";
            $table->string('child')->nullable()->comment = "配偶者や子供の有無";
            $table->string('emg_ph_no')->nullable()->comment = "緊急連絡先";
            $table->string('waste_time')->nullable()->comment = "通勤手段/時間（分）";
            $table->bigInteger('emp_id')->unsigned()->index(); //api_emp_id
            $table->timestamps();

            $table->foreign('emp_id')->references('emp_id')->on('employees')->onDelete('cascade');
            
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
