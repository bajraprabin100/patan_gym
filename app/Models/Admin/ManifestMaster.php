<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ManifestMaster extends Model
{
    protected $table = 'manifest_master';
    protected $fillable = [
        'manifest_no', 'manifest_date', 'location_from', 'location_to', 'remarks', 'prepared_by', 'prepared_on', 'received_by', 'received_on','export_tag','branch_code','posted_date','receive_branch',
        'receive_timestamp','type'
    ];
}
