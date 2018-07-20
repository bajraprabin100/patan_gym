<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills_record', function (Blueprint $table) {
            $table->increments('id');
            $table->string('membership_no');
            $table->integer('bill_no')->unique();
            $table->integer('amount',10,2);
            $table->float('discount',10,2);
            $table->float('paid_amount',10,2);
            $table->float('due_amount',10,2);
            $table->text('remarks');
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
        Schema::dropIfExists('bills_record');
    }
}
