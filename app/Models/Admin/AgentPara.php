<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AgentPara extends Model
{
    protected $table='agent_para';
    protected $fillable=[
        'agent_id','agent_name','address','telephone','fax_no','email','ceo_md','ceo_mobileno','contract_person','mobile_no','type','remarks','branch_code'
    ];
}
