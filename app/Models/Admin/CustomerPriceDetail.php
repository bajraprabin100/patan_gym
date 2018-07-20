<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CustomerPriceDetail extends Model
{
    protected $table='customer_price_detail';
    protected $fillable=[
        'user_id','customer_code','effective_date_from','effective_date_to','merchandise_type','rate','remarks','location_code','mailing_mode'
    ];
}
