<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PodRecordDetail extends Model
{

    protected $fillable = [
        'pod_master_id','record_no','bill_no'

    ];
}
