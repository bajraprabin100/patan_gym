<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountryParaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_paras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code');
            $table->string('country_name');
            $table->string('currency_code');
            $table->string('currency_name');
            $table->string('remarks');
            $table->string('nationality_code');
            $table->string('nationality_name');
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
        Schema::dropIfExists('country_paras');
    }
}
