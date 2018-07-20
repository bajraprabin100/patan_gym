<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BillStockPara extends Model
{
    //
    protected $table="bill_stock_paras";
    protected $fillable=[
        'issue_id','branch_code','bill_no_from','bill_no_to','issued_on','issued_by','agent_id','bill_type'
    ];
}
