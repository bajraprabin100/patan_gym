<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pickup_code',30);
            $table->string('shipper_code',20);
            $table->string('consignee_name');
            $table->string('consignee_address');
            $table->string('location_code',20);
            $table->string('mobile_no',20);
            $table->string('telephone_no',20);
            $table->string('merchandise_code',20);
            $table->char('mailing_mode',1);
            $table->float('quantity',10,2);
            $table->float('weight',10,2);
            $table->text('description');
            $table->string('bill_no');
            $table->char('book_tag',1);
            $table->string('branch_code',20);
            $table->string('fiscal_year',20);
            $table->string('crossing_no',20);
            $table->integer('SN');
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
        Schema::dropIfExists('pickup_detail');
    }
}
