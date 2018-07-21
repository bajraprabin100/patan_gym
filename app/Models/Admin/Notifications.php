<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'message', 'reference_id','type'
    ];
}
