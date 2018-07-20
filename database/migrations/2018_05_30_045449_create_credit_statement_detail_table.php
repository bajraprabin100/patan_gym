<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditStatementDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_statement_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('statement_master_id')->unsigned();
            $table->foreign('statement_master_id')->references('id')->on('credit_statement_master')->onDelete('cascade');
            $table->string('statement_no',30);
            $table->date('bill_date');
            $table->string('bill_no',20);
            $table->string('remarks');
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
        Schema::dropIfExists('credit_statement_detail');
    }
}
