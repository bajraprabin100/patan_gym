<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class GroupCode extends Model
{
    protected $table = 'group_code';
    protected $fillable=[
        'group_code','group_name','branch_code'
    ];

}