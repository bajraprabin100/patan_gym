<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class BookingInformation extends Model
{
    protected $table='booking_information';
    protected $fillable=[
        'bill_no','book_date','shipper_code','sender_name','sender_number','consignee_name','consignee_address','dest_location_code','org_location_code','consignee_telephone_no',
        'consignee_mobile_no','merchandise_code','mailing_mode','quantity','weight','description','payment_mode','weight_charge','other_charge','taxable_amount','vat','declared_value','voucher_no',
        'voucher_code','cheque_no','transaction_id','prepared_by','prepared_on','branch_code','manifest_no','amount','total_amount','statement_no','zone_code','export_tag',
        'delivery_no','crossing_no','length','breadth','height','cod_amount'

    ];
    public function generateBill($attributes){

        $query = BillStockDetail::select(DB::raw('MAX(bill_no+1) as bill_no'))
                          ->where('branch_code','=',$attributes['branch_code'])->where('bill_type','=','A')->first();
        if($query->bill_no!=null){
            $check_bill=BillStockPara::where('branch_code','=',$attributes['branch_code'])
                                       ->first();
            if( ($check_bill->bill_no_from <=$query->bill_no) && ($check_bill->bill_no_to >= $query->bill_no)){
                $check_bill->bill_no=$query->bill_no;
                return $check_bill;
            }
            else
                 return null;

        }
        else{
            $check_bill=BillStockPara::where('branch_code','=',$attributes['branch_code'])
                ->first();
            if(!$check_bill)
                return null;
            $check_bill->bill_no=$check_bill->bill_no_from;
            return $check_bill;
        }
    }
}
