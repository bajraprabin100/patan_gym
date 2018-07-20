<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class RoutePara extends Model
{
    protected $table='route_para';
    protected $fillable=[
        'route_code','route_name','remarks','pickup_tag','delivery_tag','branch_code','location_code'
    ];
}
