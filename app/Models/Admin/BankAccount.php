<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $table='bank_accounts';
    protected $fillable=[
        'date','particular','debit_amount','credit_amount'
    ];
}
