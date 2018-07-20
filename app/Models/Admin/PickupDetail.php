<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PickupDetail extends Model
{
    protected $table='pickup_detail';
    protected $fillable=['pickup_code','shipper_code','consignee_name','consignee_address',
        'location_code','mobile_no','telephone_no','merchandise_code',
        'mailing_mode','quantity','weight','description','bill_no','book_tag',
        'branch_code','fiscal_year','crossing_no','SN'
    ];

}
