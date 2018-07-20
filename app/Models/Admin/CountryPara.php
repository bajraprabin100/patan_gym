<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CountryPara extends Model
{
    //
    protected $table='country_paras';
    protected $fillable=[
        'country_code','country_name','currency_code','currency_name','remarks','nationality_code','nationality_name'
    ];

}
