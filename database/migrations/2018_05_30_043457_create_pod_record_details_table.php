<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePodRecordDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pod_record_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pod_master_id');
            $table->foreign('pod_master_id')->references('id')->on('pod_record_masters')->onDelete('cascade');
            $table->string('record_no');
            $table->integer('bill_no');
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
        Schema::dropIfExists('pod_record_details');
    }
}
