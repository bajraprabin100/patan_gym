<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillStockDetal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_stock_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('issue_id');
            $table->string('bill_no');
            $table->char('used_tag');
            $table->string('branch_code');
            $table->string('fiscal_year');
            $table->string('bill_type');
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
        Schema::dropIfExists('bill_stock_detail');
    }
}
