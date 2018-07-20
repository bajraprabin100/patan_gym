<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ZoneDetail extends Model
{
    //
    protected $table='zone_detail';
    protected $fillable=[
        'zone_code','merchandise_code','mailing_mode','location_code','branch_code'
        ];
}
