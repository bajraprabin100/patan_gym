<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table='company_informations';
    protected $fillable = [
        'company_code','company_name','address','vat_no','email','phone','fax','id'
    ];
}
