<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManifestDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('manifest_no');
            $table->foreign('manifest_no')->references('manifest_no')->on('manifest_master');
            $table->integer('bill_no');
            $table->string('shipper_code',20);
            $table->string('consignee_name',100);
            $table->string('consignee_address');
//            $table->string('location_code',20);
            $table->string('location_from',20);
            $table->string('location_to',20);
           $table->string('merchandise_code',20);
           $table->string('quantity',10);
           $table->string('weight',10);
           $table->string('receive_condition',10);
           $table->char('consignee_receive',1)->nullable();
           $table->date('consignee_receive_date')->nullable();
           $table->string('remarks');
           $table->string('branch_code',20);
           $table->string('delivery_taken_by')->nullable();
           $table->date('delivery_taken_on')->nullable();
           $table->string('manifest_no_made');
           $table->string('rto',1);
           $table->string('fiscal_year');
           $table->string('manifest_no_rto');
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
        Schema::dropIfExists('manifest_detail');
    }
}
