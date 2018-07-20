<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillStockParaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_stock_paras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('issue_id')->unique;
            $table->string('branch_code');
            $table->string('bill_no_from');
            $table->string('bill_no_to');
            $table->date('issued_on');
            $table->integer('issued_by');
            $table->string('agent_id');
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
        Schema::dropIfExists('bill_stock_paras');
    }
}
