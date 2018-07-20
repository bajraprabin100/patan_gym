<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CreditStatementDetail extends Model
{
    protected $table='credit_statement_detail';
   protected $fillable=['statement_master_id','statement_no','bill_date','bill_no','remarks'];
}
