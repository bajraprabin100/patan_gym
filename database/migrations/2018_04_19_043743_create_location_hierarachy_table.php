<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationHierarachyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_hierarchy', function (Blueprint $table) {
            $table->increments('id');
            $table->string('location_code')->unique();
            $table->string('location_name');
            $table->string('master_location_code');
            $table->string('category');
            $table->string('location_type');
            $table->string('branch_name');
            $table->string('contact_name');
            $table->string('contact_number');
            $table->string('email');
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
        Schema::dropIfExists('location_hierarchy');
    }
}
