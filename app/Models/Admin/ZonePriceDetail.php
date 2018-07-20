<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ZonePriceDetail extends Model
{
    protected $table='zonewise_price_detail';
    protected $fillable=[
        'zone_code','price_code','document_type','weight','price','effective_date_from','effective_date_to'
    ];
}
