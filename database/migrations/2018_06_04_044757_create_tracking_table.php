<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking_id', 20);
            $table->date('track_date');
            $table->string('bill_no');
            $table->string('reference_no');
            $table->string('status', 50);
            $table->string('activity', 150);
            $table->string('location');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('timestamp')->nullable();
            $table->string('tag', 2);
            $table->string('branch_code', 20);
            $table->string('crossing_no', 20);
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
        Schema::dropIfExists('tracking');
    }
}
