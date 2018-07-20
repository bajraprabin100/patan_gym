<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class LocationHierarachy extends Model
{
    protected $table='location_hierarchy';
    protected $fillable=[
        'location_code','location_name','master_location_code','category','location_type','branch_name','contact_name','contact_number','email'
    ];
}
