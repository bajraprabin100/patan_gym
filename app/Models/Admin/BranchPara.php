<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BranchPara extends Model
{
    protected $table='branch_paras';
    protected $fillable=[
        'branch_code','branch_name','group_code','group_name',
        'address','vat_no','phone','email','fax',
        'branch_company_name','receiving_branch_name','receiving_branch_code','delivery_group_code','delivery_group_name','cod','vat_applicable',
        'branch_incharge_name','mobile_no'
    ];
}
