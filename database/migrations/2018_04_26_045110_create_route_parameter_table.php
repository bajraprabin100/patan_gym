<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_para', function (Blueprint $table) {
            $table->increments('id');
            $table->string('route_code',10);
            $table->string('route_name',100);
            $table->string('remarks');
            $table->string('pickup_tag',2);
            $table->string('delivery_tag',2);
            $table->string('branch_code');
            $table->string('location_code');
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
        Schema::dropIfExists('route_para');
    }
}
