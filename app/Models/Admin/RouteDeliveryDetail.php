<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class RouteDeliveryDetail extends Model
{
    protected $table = 'route_delivery_detail';
    protected $fillable = [
        'delivery_no','manifest_no','master_id','bill_no', 'consignee_name', 'consignee_address', 'telephone_no', 'mobile_no',
        'merchandise_code', 'weight', 'quantity', 'received_by', 'received_on', 'remarks', 'branch_code',
        'rto', 'recent_date', 'sno'
    ];
}
