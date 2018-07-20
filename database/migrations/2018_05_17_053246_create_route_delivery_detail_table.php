<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteDeliveryDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_delivery_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('master_id')->unsigned();
            $table->foreign('master_id')->references('id')->on('route_delivery_master')->onDelete('cascade');
            $table->string('manifest_no');
            $table->string('bill_no');
            $table->string('consignee_name');
            $table->string('consignee_address');
            $table->string('telephone_no');
            $table->string('mobile_no');
            $table->string('merchandise_code');
            $table->float('weight',10,2);
            $table->float('quantity',10,2);
            $table->string('received_by');
            $table->date('received_on')->nullable();
            $table->string('remarks');
            $table->string('branch_code',30);
            $table->char('rto',1);
            $table->date('recent_date');
            $table->integer('sno');
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
        Schema::dropIfExists('route_delivery_detail');
    }
}
