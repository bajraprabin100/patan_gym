<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BillStockDetail extends Model
{
    protected $table="bill_stock_detail";
    protected $fillable=[
        'issue_id','branch_code','used_tag','bill_no','used_tag','fiscal_year','bill_type'
    ];
}
