<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchParaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_paras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branch_code')->unique();
            $table->string('branch_name');
            $table->string('group_code');
            $table->string('group_name');
            $table-string('delivery_group_code',10);
            $table-string('delivery_group_name',100);
            $table->enum('cod', ['Yes', 'No']);
            $table->enum('vat_applicable', ['Yes', 'No']);
            $table->string('address');
            $table->string('vat_no');
            $table->string('phone');
            $table->string('email');
            $table->string('fax');
            $table->string('branch_company_name');
            $table->string('receiving_branch_name');
            $table->string('receiving_branch_code');
            $table->string('branch_incharge_name');
            $table->string('mobile_no',20);
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
        Schema::dropIfExists('branch_paras');
    }
}
