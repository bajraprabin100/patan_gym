<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CreditStatementMaster extends Model
{
    protected $table='credit_statement_master';
    protected $fillable=['statement_no','statement_date','date_from','date_to','shipper_code',
         'branch_code','prepared_by','prepared_on','posted_date'];
}
