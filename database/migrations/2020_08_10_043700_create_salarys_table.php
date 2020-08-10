<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salarys', function (Blueprint $table) {
            $table->id();
            $table->decimal('trans_money',10, 2);
            $table->integer('jlpt');
            $table->integer('bonus');
            $table->integer('income_tax');
            $table->decimal('ssb',10, 2);
            $table->decimal('leave_late',10, 2);
            $table->decimal('salary_amount',10, 2);
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
        Schema::dropIfExists('salarys');
    }
}
