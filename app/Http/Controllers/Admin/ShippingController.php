<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\CountryPara;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BranchPara;
use App\Models\Admin\BillStockPara;
use DB;
use App\Models\Admin\ShippingPara;
use Session;

class ShippingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $this->admin_data['shipping_paras']=ShippingPara::orderBy('id','desc')->get();
        $this->admin_data['country_paras']=CountryPara::orderBy('id','desc')->get();
        return view('admin.shipping.index',$this->admin_data);
    }
    public function store(Request $request){
        $data=$request->all();
        $query=ShippingPara::select(DB::raw("MAX(CAST(SUBSTRING(shipper_code, 6) AS UNSIGNED)+1) AS Shipper_code"))->first();
        if($query!=null) {
            $shipper_code = 'S' . $this->admin_data['login_user']->branch_code . '-' . $query->Shipper_code;
        }else{
            $shipper_code = 'S' . $this->admin_data['login_user']->branch_code . '-' . '1';
        }
        if(!isset($request->used_tag)){
            $data['used_tag']='N';
        }
        $data['branch_code']='HOD';
        $data['shipper_code']=$shipper_code;
//        $data['issued_by']=Auth::user()->id;
        ShippingPara::create($data);
        Session::flash('successMsg',$shipper_code.' has been generated and shipper parameter saved successfully');
        return response()->json(['success'=>true,'message'=>'Shipper parameter saved successfully'],200);
    }


}
