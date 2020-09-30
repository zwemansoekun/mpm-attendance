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
            $table->integer('total_amount')->comment = "総額";
            $table->integer('c_paid')->comment = "会社負担分";  
            $table->string('remark')->nullable()->comment = "備考";
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
