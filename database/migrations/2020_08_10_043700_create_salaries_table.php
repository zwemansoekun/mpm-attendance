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
            $table->string('pay_month')->comment = "year/month";
            $table->integer('income')->comment = "基本給";        
            $table->integer('trans_money')->nullable()->comment = "通勤交通費";
            $table->integer('jlpt')->nullable()->comment = "JLPT";
            $table->integer('bonus')->nullable()->comment = "ボーナス";
            $table->integer('income_tax')->comment = "所得税";
            $table->integer('ssb')->comment = "SSB";
            $table->integer('total_salary')->comment = "合計";      
            $table->integer('leave_late')->nullable()->comment = "遅刻欠勤早退";      
            $table->integer('payment_amount')->comment = "支給額"  ;      
            $table->integer('employee_id'); // f_key
            $table->integer('ssb_id'); //f_key //check pay pr
            $table->integer('adju1')->nullable()->comment = "その他調整(控除の場合-)"; //adjustments (in case of deduction-)
            $table->integer('adju2')->nullable()->comment = "その他調整(控除の場合-)"; 
            $table->integer('adju3')->nullable()->comment = "その他調整(控除の場合-)"; 
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
