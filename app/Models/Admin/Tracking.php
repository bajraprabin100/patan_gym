<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table='tracking';
    protected $fillable=[
        'tracking_id','track_date','bill_no','reference_no','status','activity','location','user_id','timestamp','tag','branch_code','crossing_no'
    ];
}
