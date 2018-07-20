<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePodRecordMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pod_record_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('record_no',30);
            $table->date('record_date');
            $table->string('prepared_by',100);
            $table->string('branch_code',20);
            $table->date('timestamp');
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
        Schema::dropIfExists('pod_record_masters');
    }
}
