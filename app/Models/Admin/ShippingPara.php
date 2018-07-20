<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ShippingPara extends Model
{
    protected $table='shipper_paras';
    protected $fillable=[
              'shipper_code','shipper_name','address','country_code','phone','fax','mobile','customer_code','branch_code','used_tag'
    ];
}
