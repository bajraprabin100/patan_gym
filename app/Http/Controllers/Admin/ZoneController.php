<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\CountryPara;
use App\Models\Admin\ZoneDetail;
use App\Models\Admin\ZoneMaster;
use App\Models\Admin\ZonePriceDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BillStockPara;
use App\Models\Admin\BranchPara;
use Session;
use DB;
use Auth;
use App\Models\Admin\LocationHierarachy;

class ZoneController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (Auth::user()->can('zone_wise_price') || Auth::user()->userPermissionCan('zone_wise_price')) {
                return $next($request);

            }
            return $next($request);
        });


    }

    public function index()
    {
        $this->admin_data['zone_price_details'] = ZoneMaster::select('zone_master.*')
            ->where('zone_master.branch_code','=',$this->admin_data['login_user']->branch_code)
          ->orderBy('zone_master.id', 'desc')->get();
        $this->admin_data['bill_issues'] = BillStockPara::all();
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->get();
        return view('admin.zone_wise_price.index', $this->admin_data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $zone = ZoneMaster::where([
                    ['zone_code', '=', $request->zone_code],
                ]
            )->first();
            if (!$zone)
                ZoneMaster::create(['zone_code' => $request->zone_code, 'zone_name' => $request->zone_name,'dox_price'=>$request->tempDoxPrice,'ndx_price'=>$request->tempNdoxPrice,'effective_date_from'=>isset($request->effective_from)?$request->effective_from:null,'effective_date_to'=> null,'remarks' => isset($request->remarks)?$request->remarks:'', 'branch_code' => $this->admin_data['login_user']->branch_code]);
                $zone_check = ZoneDetail::where([
                        ['zone_code', '=', $request->zone_code],
                        ['location_code', '=', $request->location_code],
                    ]
                )->first();
                if (!$zone_check) {
                    $zone_detail = ZoneDetail::create(['zone_code' => $request->zone_code, 'merchandise_code' => "", 'mailing_mode' => $request->mailing_mode, 'location_code' => $request->location_code, 'branch_code' => $this->admin_data['login_user']->branch_code]);
                }

//            if ($request->tempNdoxPrice != "") {
//                $query = ZoneDetail::select(DB::raw('MAX(price_code+1) as price_code'))->first();
//                $priceCode = $query->price_code == null ? '1' : $query->price_code;
//                $zone_check = ZoneDetail::where([
//                        ['zone_code', '=', $request->zone_code],
//                        ['location_code', '=', $request->location_code],
//                        ['merchandise_type', '=', 'NDX']
//                    ]
//                )->first();
//                if ($zone_check) {
//                    ZonePriceDetail::where('price_code', '=', $zone_check->price_code)->update(['price' => $request->tempNdoxPrice]);
//                } else {
//                    $zone_detail = ZoneDetail::create(['zone_code' => $request->zone_code, 'price_code' => $priceCode, 'merchandise_type' => 'NDX', 'merchandise_code' => "", 'mailing_mode' => $request->mailing_mode, 'location_code' => $request->location_code, 'branch_code' => $this->admin_data['login_user']->branch_code]);
//                    $tempWeight = 1;
//                    $zone_price = ZonePriceDetail::create(['zone_code' => $request->zone_code, 'price_code' => $priceCode, 'weight' => $tempWeight, 'price' => $request->tempNdoxPrice, 'effective_date_from' => $request->effective_from, 'effective_date_to' => $request->effective_to]);
//                }
//            }
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            // something went wrong
        }
        $zone_detail_query = ZoneDetail::select( 'l.*','zm.*','zone_detail.*')->join('zone_master as zm','zm.zone_code','=','zone_detail.zone_code')->where('zone_detail.zone_code', '=', $request->zone_code)->join('location_hierarchy as l','l.location_code','=','zone_detail.location_code')->get();
        $zone_detail_html = view('admin.zone_wise_price.details', compact('zone_detail_query'))->render();
        return response()->json(['success' => true, 'message' => 'Zone Wise Price added successfully', 'data' => ['zone_detail_html' => $zone_detail_html]], 200);


    }

    public function edit(Request $request, $id)
    {
        $this->admin_data['zone_code'] = ZoneMaster::where('zone_master.zone_code', '=', $id)->first();
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC') ->where('location_type','=','PLC')
            ->get();

        $this->admin_data['zone_details'] =ZoneMaster::select('zone_master.*','l.*','zone_detail.*','zone_detail.id as zd_id')
        ->where('zone_master.zone_code', '=', $id)
            ->join('zone_detail', 'zone_detail.zone_code', '=', 'zone_master.zone_code')
            ->join('location_hierarchy as l','l.location_code','=','zone_detail.location_code')
            ->get();
        return view('admin.zone_wise_price.edit', $this->admin_data);


    }

    public function storeNewZone(Request $request)
    {
        ZoneMaster::where('zone_code','=',$request->zone_code)->update(['dox_price'=>$request->tempDoxPrice,'ndx_price'=>$request->tempNdoxPrice,'effective_date_from'=>isset($request->effective_from)?$request->effective_from:null,'effective_date_to'=> null,'remarks' => isset($request->remarks)?$request->remarks:'', 'branch_code' => $this->admin_data['login_user']->branch_code]);
    $zone_detail=ZoneDetail::where([
        ['zone_code','=',$request->zone_code],
        ['location_code','=', $request->location_code]
    ])
        ->first();

        if (!$zone_detail) {
            ZoneDetail::create(['zone_code' => $request->zone_code, 'merchandise_code' => "", 'mailing_mode' => $request->mailing_mode, 'location_code' => $request->location_code, 'branch_code' => $this->admin_data['login_user']->branch_code]);
        }
//        if ($request->tempNdoxPrice != "") {
//            $query = ZoneDetail::select(DB::raw('MAX(price_code+1) as price_code'))->first();
//            $priceCode = $query->price_code == null ? '1' : $query->price_code;
//            $zone_detail = ZoneDetail::create(['zone_code' => $request->zone_code, 'price_code' => $priceCode, 'merchandise_type' => 'NDX', 'merchandise_code' => "", 'mailing_mode' => $request->mailing_mode, 'location_code' => $request->location_code, 'branch_code' => $this->admin_data['login_user']->branch_code]);
//            $tempWeight = 1;
//            $zone_price = ZonePriceDetail::create(['zone_code' => $request->zone_code, 'price_code' => $priceCode, 'weight' => $tempWeight, 'price' => $request->tempDoxPrice, 'effective_date_from' => $request->effective_from, 'effective_date_to' => $request->effective_to]);
//
//        }
        Session::flash('successMsg','Updated Successfully');
        return response()->json(['success' => true, 'message' => 'Zone Wise Price added successfully'], 200);
    }

    public function update_zone_wise_price(Request $request, $id)
    {
//
//        DB::beginTransaction();
//        try {
//            $zone_detail=ZoneDetail::find($id);
//            ZoneDetail::where('id','=',$id)->update(['zone_code' => $request->zone_code, 'mailing_mode' => $request->mailing_mode, 'location_code' => $request->location_code]);
//           ZonePriceDetail::where('price_code','=',$zone_detail->price_code)->update(['zone_code' => $request->zone_code, 'effective_date_from' => $request->effective_from, 'effective_date_to' => $request->effective_to, 'price' => $request->tempDoxPrice]);
//            ZoneMaster::where('zone_code','=',$zone_detail->zone_code)->update(['zone_code' => $request->zone_code, 'zone_name' => $request->zone_name, 'remarks' => $request->remarks]);
//
//            DB::commit();
////            // all good
//        } catch (\Exception $e) {
//            DB::rollback();
//            dd($e->getMessage());
//            // something went wrong
//        }
//
//        Session::flash('successMsg', 'Zone Price Detail updated successfully');
//        return response()->json(['success' => true, 'message' => 'Zone Price Detail updated successfully'], 200);
    }

    public function destroy_zone_wise_price($id)
    {


        $zone_master = ZoneMaster::find($id);
        ZoneMaster::where('id', '=', $id)->delete();
        ZoneDetail::where('zone_code', '=', $zone_master->zone_code)->delete();
        Session::flash('successMsg', 'Zone Wise Price Parameter has been deleted successfully');
        return response()->json(['success' => true, 'message' => 'Zone Wise Price Parameter has been deleted successfully'], 200);

    }

    public function deleteDetails(Request $request)
    {
        $zone_detail = ZoneDetail::where('id', '=', $request->zone_detail_id)->first();
        $zone_detail->delete();
        $zone_detail_query = ZoneDetail::select('zm.*', 'zone_detail.*')->join('zone_master as zm', 'zm.zone_code', '=', 'zone_detail.zone_code')->join('location_hierarchy as l','l.location_code','=','zone_detail.location_code')->where('zone_detail.zone_code', '=', $request->zone_code)->get();
        $zone_detail_html = view('admin.zone_wise_price.details', compact('zone_detail_query'))->render();
        return response()->json(['success' => true, 'message' => 'Zone Wise Price deleted successfully', 'data' => ['zone_detail_html' => $zone_detail_html]], 200);

    }
    public function deleteDtl($id){

        $zone_detail = ZoneDetail::where('id', '=', $id)->first();
        $zone_detail->delete();
        Session::flash('successMsg','Successfully deleted');
        return redirect()->back();
    }

}