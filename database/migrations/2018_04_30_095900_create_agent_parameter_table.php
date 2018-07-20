<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_para', function (Blueprint $table) {
            $table->increments('id');
            $table->string('agent_id');
            $table->string('agent_name');
            $table->string('address');
            $table->string('telephone',20);
            $table->string('fax_no',20);
            $table->string('email');
            $table->string('ceo_md',40);
            $table->string('ceo_mobileno',40);
            $table->string('contract_person',40);
            $table->string('mobile_no',40);
            $table->char('type',1);
            $table->string('remarks',100);
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
        Schema::dropIfExists('agent_para');
    }
}
