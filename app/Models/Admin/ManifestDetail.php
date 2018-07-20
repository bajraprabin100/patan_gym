<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ManifestDetail extends Model
{
    protected $table='manifest_detail';
    protected $fillable=[
      'manifest_no','bill_no','shipper_code','consignee_name','consignee_address','location_code','merchandise_code','quantity','weight','receive_condition','consignee_receive','consignee_receive_date','remarks','branch_code',
        'delivery_taken_by','delivery_taken_on','manifest_no_made','rto','fiscal_year','manifest_no_rto','location_from','location_to'
    ];
}
