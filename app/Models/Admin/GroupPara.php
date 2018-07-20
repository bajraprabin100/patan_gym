<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class GroupPara extends Model
{
    protected $table='group_para';
    protected $fillable=[
        'group_code','group_name','branch_code','location_code'
    ];
}
