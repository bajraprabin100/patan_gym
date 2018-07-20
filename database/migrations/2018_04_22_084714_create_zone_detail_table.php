<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoneDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('zone_code',20);
            $table->string('price_code',40);
            $table->string('merchandise_type',10);
            $table->string('merchandise_code',10);
            $table->string('mailing_mode',10);
            $table->string('location_code',10);
            $table->string('branch_code',10);
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
        Schema::dropIfExists('zone_detail');
    }
}
