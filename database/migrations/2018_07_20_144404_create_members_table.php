<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('membership_no');
            $table->string('name');
            $table->string('address');
            $table->date('user_valid_date');
            $table->string('gender');
            $table->date('admission_date');
            $table->float('package_rate', 10, 2);
            $table->string('email');
            $table->string('contact');
            $table->string('photo');
            $table->string('user_status');
            $table->boolean('is_member');
//            $table->string('bill_no');
//            $table->string('discount');
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
        Schema::dropIfExists('members');
    }
}
