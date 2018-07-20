<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditStatementMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_statement_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('statement_no',30);
            $table->date('statement_date');
            $table->date('date_from');
            $table->date('date_to');
            $table->string('shipper_code',20);
            $table->string('branch_code',20);
            $table->string('prepared_by',100);
            $table->date('prepared_on');
            $table->date('posted_date')->nulllable();
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
        Schema::dropIfExists('credit_statement_master');
    }
}
