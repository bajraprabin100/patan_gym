<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipperParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipper_paras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shipper_code');
            $table->string('shipper_name');
            $table->string('address');
            $table->string('country_code');
            $table->string('phone');
            $table->string('fax');
            $table->string('mobile');
            $table->string('customer_code');
            $table->string('branch_code');
            $table->string('used_tag');

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
        Schema::dropIfExists('shipper_paras');
    }
}
