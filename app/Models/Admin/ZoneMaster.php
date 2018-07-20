<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ZoneMaster extends Model
{
    protected $table='zone_master';
    protected $fillable=[
      'zone_code','zone_name','remarks','branch_code','dox_price','ndx_price','effective_date_from','effective_date_to'
    ];
}
