<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteDeliveryMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_delivery_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('delivery_no',30)->unique();
            $table->date('delivery_date');
            $table->string('delivered_by');
            $table->string('route',20);
            $table->string('remarks');
            $table->string('branch_code');
            $table->string('receive_entered_by');
            $table->date('receive_entered_on')->nullable();
            $table->string('delivery_entered_by');
            $table->date('delivery_entered_date');
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
        Schema::dropIfExists('route_delivery_master');
    }
}
