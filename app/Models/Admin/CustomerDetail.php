<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    protected $table='customer_detail';
    protected $fillable=[
        'user_id','customer_code','zone_code','discount_pct'
    ];
}
