<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_para', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('company_code',30);
            $table->string('customer_code',20);
            $table->string('customer_name');
            $table->string('address');
            $table->string('country_code',10);
            $table->string('shipper_code',10);
            $table->string('shipper_name',100);
            $table->string('phone');
//            $table->string('email');
            $table->string('fax');
            $table->enum('cod', ['Yes', 'No']);
            $table->enum('payment_type', ['cash', 'credit','two_pay']);
            $table->string('mobile');
            $table->string('vat_no');
            $table->string('vat_applicable');
            $table->string('ac_code');
            $table->string('branch_code');
            $table->string('used_tag');
            $table->string('function_type',2);
            $table->string('delivery_hrs');
            $table->string('tracking_report');
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
        Schema::dropIfExists('customer_para');
    }
}
