<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerPriceDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_price_detail', function (Blueprint $table) {
            $table->increments('id');
//            $table->integer('user_id')->unsigned()->references('id')->on('users');
            $table->string('customer_code',20);
            $table->date('effective_date_from');
            $table->date('effective_date_to');
            $table->string('mailing_mode',10);
            $table->string('merchandise_type',20);
            $table->string('location_code',20);
            $table->float('rate',10,2);
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
        Schema::dropIfExists('customer_price_detail');
    }
}
