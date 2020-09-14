<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssbs', function (Blueprint $table) {
            $table->id();
            //$table->string('ssb_month');
            $table->integer('total_amount');
            $table->integer('c_paid');
            // $table->decimal('total_amount',10, 2);
            // $table->decimal('c_paid',10, 2);
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('ssbs');
    }
}
