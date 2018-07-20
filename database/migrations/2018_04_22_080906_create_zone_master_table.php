<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoneMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('zone_code',20)->unique();
            $table->string('zone_name',40);
            $table->float('dox_price',10,2);
            $table->float('ndx_price',10,2);
            $table->date('effective_date_from')->nullable()->default(null);
            $table->date('effective_date_to')->nullable()->default(null);
            $table->string('remarks',100);
            $table->string('branch_code', 10);
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
        Schema::dropIfExists('zone_master');
    }
}
