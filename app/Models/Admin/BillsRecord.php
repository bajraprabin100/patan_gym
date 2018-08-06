<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BillsRecord extends Model
{
    protected $table='bills_record';
    protected $fillable=[
       'date','package', 'membership_no','bill_no','amount','discount','paid_amount','due_amount','remarks','name'
    ];
}
