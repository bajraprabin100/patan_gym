<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CashBook extends Model
{
    protected $table='cash_book';
    protected $fillable=[
        'date','particular','debit_amount','credit_amount'
        ];
}
