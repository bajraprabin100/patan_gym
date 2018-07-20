<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_information', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bill_no', 20);
            $table->date('book_date');
            $table->string('shipper_code', 20);
            $table->float('cod_amount', 10, 2)->default(0);
            $table->string('sender_name', 100);
            $table->string('sender_number', 20);
            $table->string('consignee_name');
            $table->string('consignee_address');
            $table->string('dest_location_code', 20);
            $table->string('org_location_code', 20);
            $table->string('consignee_telephone_no', 20);
            $table->string('consignee_mobile_no', 20);
            $table->string('merchandise_code', 20);
            $table->string('mailing_mode', 10);
            $table->float('quantity', 10, 2);
            $table->float('weight', 10, 2);
            $table->text('description');
            $table->text('payment_mode');
            $table->float('weight_charge', 10, 2)->default(0);
            $table->float('other_charge', 10, 2)->default(0);
            $table->float('taxable_amount', 10, 2)->default(0);
            $table->float('vat', 10, 2)->default(0);
            $table->float('declared_value', 10, 2)->default(0);
            $table->string('voucher_no', 20);
            $table->string('voucher_code', 10);
            $table->string('cheque_no');
            $table->string('transaction_id');
            $table->string('prepared_by', 10);
            $table->date('prepared_on');
            $table->string('branch_code', 20);
            $table->string('manifest_no', 20);
            $table->string('amount', 20);
            $table->string('total_amount', 20);
            $table->string('statement_no', 20);
            $table->string('zone_code', 20);
            $table->char('export_tag', 1);
            $table->string('delivery_no', 20);
            $table->string('crossing_no', 20);
            $table->float('length', 20)->default(0);
            $table->float('breadth', 10, 2)->default(0);
            $table->float('height', 10, 2)->default(0);
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
        Schema::dropIfExists('booking_information');
    }
}
