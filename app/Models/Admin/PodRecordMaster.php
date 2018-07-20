<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PodRecordMaster extends Model
{
    protected  $fillable=[
        'record_no','record_date','prepared_by','branch_code','timestamp'];
}
