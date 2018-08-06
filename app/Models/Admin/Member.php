<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';
    protected $fillable = [
        'membership_no', 'name', 'address', 'user_valid_date', 'gender', 'admission_date', 'package_rate', 'email', 'contact','photo','user_status','is_member'
    ];
}
