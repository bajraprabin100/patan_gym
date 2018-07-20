<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class RouteDeliveryMaster extends Model
{
    protected $table='route_delivery_master';
    protected $fillable=[
      'delivery_no','delivery_date','delivered_by','route','remarks','branch_code','receive_entered_by','receive_entered_on',
        'receive_entered_on','delivery_entered_by','delivery_entered_date'
    ];
}
