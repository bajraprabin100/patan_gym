<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class EmployeeParameter extends Model
{
    protected $table='employee_information_para';
    protected $fillable=[
        'user_id','employee_name','address','phone','fax','mobile','currently_on_job','branch_code'
    ];
    public function role(){
        return $this->belongsTo('App\Role','user_id','user_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
