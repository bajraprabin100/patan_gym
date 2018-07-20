<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonewisePriceDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonewise_price_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('zone_code');
            $table->string('price_code');
            $table->enum('document_type',['DOX','NDX']);
            $table->string('weight');
            $table->float('price',10,2);
            $table->date('effective_date_from');
            $table->date('effective_date_to');
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
        Schema::dropIfExists('zonewise_price_detail');
    }
}
