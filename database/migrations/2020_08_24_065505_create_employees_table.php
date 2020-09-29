<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();      
            $table->string('kana_name')->comment = "名前(フリガナ)";
            $table->date('entry_date')->comment = "入社日(mm/dd/yyyy)";
            $table->date('dob')->comment = "生年月日(mm/dd/yyyy)";
            $table->bigInteger('emp_id')->unsigned()->index();
            // $table->primary('emp_id'); //api_emp_id
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
        Schema::dropIfExists('employees');
    }
}
