<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class MerchandisePara extends Model
{
    protected $table='merchandise_para';
    protected $fillable=[
        'merchandise_code','merchandise_name'
    ];
}
