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
            $table->string('prefix');
            $table->string('bill_no_from');
            $table->string('bill_no_to');
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
