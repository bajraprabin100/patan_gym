<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PickupInformation extends Model
{
    protected $table='pickup_information';
    protected $fillable=['pickup_code','pickedup_by','pickup_date','route','entered_by','branch_code','fiscal_year'];
}
