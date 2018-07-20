<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManifestMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('manifest_no')->unique();
            $table->date('manifest_date');
            $table->string('location_from',20);
            $table->string('location_to',20);
//            $table->char('mailing_mode',1);
            $table->string('remarks',100);
            $table->string('prepared_by',10);
            $table->date('prepared_on');
            $table->integer('received_by')->nullable();
            $table->date('received_on')->nullable();
            $table->char('export_tag',1);
            $table->string('branch_code',20);
            $table->date('posted_date')->nullable();
            $table->enum('type', ['automatic_bill', 'manual_bill']);
//            $table->string('merchandise_type');
            $table->string('receive_branch')->nullable();
            $table->datetime('receive_timestamp')->nullable();
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
        Schema::dropIfExists('manifest_master');
    }
}
