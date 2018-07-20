<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CustomerPara extends Model
{
   //
    protected $table='customer_para';
    protected $fillable=[
        'customer_code','user_id','company_code','customer_name','address','country_code','shipper_code','shipper_name','phone','fax','mobile','vat_no','vat_applicable','ac_code','branch_code','used_tag','function_type','delivery_hrs','tracking_report'
    ];
}
