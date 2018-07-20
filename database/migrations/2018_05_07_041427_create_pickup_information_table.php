<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_information', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pickup_code')->unique();
            $table->string('pickedup_by');
            $table->date('pickup_date');
            $table->string('route',20);
            $table->string('entered_by',20);
            $table->string('branch_code',20);
            $table->string('fiscal_year',30);
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
        Schema::dropIfExists('pickup_information');
    }
}
