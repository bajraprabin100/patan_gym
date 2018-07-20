<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BookingInformation;
use App\Models\Admin\BranchPara;
use App\Models\Admin\EmployeeParameter;
use App\Models\Admin\GroupCode;
use App\Models\Admin\ManifestDetail;
use App\Models\Admin\ManifestMaster;
use App\Models\Admin\RouteDeliveryDetail;
use App\Models\Admin\RouteDeliveryMaster;
use App\Models\Admin\RoutePara;
use App\Models\Admin\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;
use DB;
use Auth;

class RouteDeliveryController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        Session::forget('delivery_information');
        $this->admin_data['manifest_datas'] = ManifestMaster::orderBy('id', 'desc')->get();
        $this->admin_data['branch_para'] = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
        $this->admin_data['route_para'] = BranchPara::select('route_code', 'route_name')
            ->where('branch_paras.delivery_group_code', '=', $this->admin_data['branch_para']->delivery_group_code)
            ->join('route_para as r', 'r.branch_code', '=', 'branch_paras.branch_code')
            ->where('r.delivery_tag', '=', 'Y')
            ->groupBy('route_code', 'route_name')
            ->orderBy('route_name', 'asc')
            ->get();
        $this->admin_data['group_data'] = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->get();
//        $this->admin_data['route_para'] = RoutePara::where([
//            ['delivery_group_code', '=', $this->admin_data['login_user']->branch_code],
//        ])->distinct()->orderBy('route_name', 'desc')->get();
        $this->admin_data['delivery_employee'] = BranchPara::where('branch_paras.delivery_group_code', '=', $this->admin_data['branch_para']->delivery_group_code)
            ->join('employee_information_para', 'employee_information_para.branch_code', '=', 'branch_paras.branch_code')
            ->join('role_user', 'role_user.user_id', '=', 'employee_information_para.user_id')
            ->join('users', 'users.id', '=', 'employee_information_para.user_id')
//            ->where('role_user.role_id', '=', 5)
            ->orderBy('employee_information_para.employee_name', 'asc')
            ->get();
        return view('admin.route_delivery.index', $this->admin_data);
    }

    public function addBill(Request $request)
    {
//        if($tag == "Y")
//        {
//            $tempBillNo = $api_data->tempBillNo;
//            $query = "SELECT BILL_NO, CONSIGNEE_CODE, CONSIGNEE_NAME, CONSIGNEE_ADDRESS, TELEPHONE_NO, MOBILE_NO,
//						MERCHANDISE_TYPE, GET_MERCHANDISE_NAME(MERCHANDISE_CODE) MERCHANDISE, MERCHANDISE_CODE,
//						QUANTITY, WEIGHT, MANIFEST_NO, FISCAL_YEAR
//					FROM
//					(
//						SELECT BILL_NO, CONSIGNEE_CODE, CONSIGNEE_NAME, CONSIGNEE_ADDRESS, TELEPHONE_NO, MOBILE_NO,
//							MERCHANDISE_TYPE, MERCHANDISE_CODE, QUANTITY, WEIGHT, MANIFEST_NO, FISCAL_YEAR
//						FROM BKIN
//						WHERE BILL_NO = '$tempBillNo'
//							AND DEST_LOCATION_CODE IN (SELECT LOCATION_CODE FROM RP
//								WHERE BRANCH_CODE = '".$api_data->branchCode."')
//
//						UNION
//						SELECT BILL_NO, CONSIGNEE_CODE, CONSIGNEE_NAME, CONSIGNEE_ADDRESS, TELEPHONE_NO, MOBILE_NO,
//							MAND.MERCHANDISE_TYPE, MERCHANDISE_CODE, QUANTITY, WEIGHT, MANM.MANIFEST_NO, MANM.FISCAL_YEAR
//						FROM MANM, MAND
//						WHERE MANM.MANIFEST_NO = MAND.MANIFEST_NO
//							AND BILL_NO = '$tempBillNo'
//							AND NVL(CONSIGNEE_RECEIVE,'N') = 'N'
//							AND DELIVERY_TAKEN_BY IS NULL
//							AND DELIVERY_TAKEN_ON IS NULL
//							AND CONSIGNEE_RECEIVE_DATE IS NULL
//							AND NVL(RECEIVE_CONDITION,'N') = 'R'
//							AND MANIFEST_NO_MADE IS NULL
//							AND MAND.BRANCH_CODE <> '".$api_data->branchCode."'
//
//					)
//					ORDER BY CONSIGNEE_NAME";
//
//            $resDeliveryFetch = $conn->selectdatafromtable($c, $query);
//        $this->admin_data['branch_para'] = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
        $add_bill = BookingInformation::select('manifest_detail.*','booking_information.*')
            ->join('route_para', 'route_para.location_code', '=', 'booking_information.dest_location_code')
            ->where('route_para.route_code','=',$request->route)
            ->leftjoin('manifest_detail', 'manifest_detail.bill_no', '=', 'booking_information.bill_no')
            ->where('booking_information.bill_no', '=', $request->bill_no)
            ->where('route_para.branch_code', '=', $this->admin_data['login_user']->branch_code)
            ->first();
        if (!$add_bill) {
            $add_bill = ManifestDetail::where('manifest_detail.bill_no', '=', $request->bill_no)
                ->join('route_para', 'route_para.location_code', '=', 'booking_information.location_to')
                ->where('route_para.route_code','=',$request->route)
                ->where('route_para.branch_code', '=', $this->admin_data['login_user']->branch_code)
                ->first();
        }

        if ($add_bill == null) {
            return response()->json(['success' => false, 'message' => 'Bill mismatch or no bill found', 'data' => null], 200);
        }
        $delivery_information = Session::get('delivery_information');
        if (!isset($delivery_information))
            $delivery_information = new Collection();
//        $booking_information='';
        $push = 'Y';
        $add = (object)$add_bill->getAttributes();
        if (isset($delivery_information)) {

            foreach ($delivery_information as $b) {
                if ($b->bill_no == $request->bill_no) {
                    $push = 'N';
                }
            }
        }
        if ($push == 'Y') {
            $delivery_information->push($add);
        }
        Session::put('delivery_information', $delivery_information);
        $add_bill_html = view('admin.route_delivery.add_bill_no', compact('delivery_information'))->render();
        return response()->json(['success' => true, 'message' => 'Bill no Successfully added', 'data' => ['add_bill_html' => $add_bill_html]], 200);


    }

    public function deleteBill(Request $request)
    {
        $delivery_information = Session::get('delivery_information');
        foreach ($delivery_information as $key => $g) {
            if ($g->bill_no == $request->bill_no) {
                $delivery_information->forget($key);
                break;
            }
        }
        $delivery_information_html = view('admin.route_delivery.add_bill_no', compact('delivery_information'))->render();
        return response()->json(['success' => true, 'message' => 'Bill no Successfully deleted', 'data' => ['add_bill_html' => $delivery_information_html]], 200);

    }

    public function store(Request $request)
    {
        if($request->delivery_no==null) {
            $delivery_information = Session::get('delivery_information');
            $query = RouteDeliveryMaster::select(DB::raw("MAX(CAST(SUBSTRING(delivery_no, 11) AS UNSIGNED)+1) AS new_delivery_no"))->first();
            if ($query->new_delivery_no != null) {

                $delivery_no = date('Y') . $this->admin_data['login_user']->branch_code . '-00' . $query->new_delivery_no;
            } else {
                $delivery_no = date('Y') . $this->admin_data['login_user']->branch_code . '-' . '001';
            }
            $delivery_information = Session::get('delivery_information');
            DB::beginTransaction();
            try {
                $fillable = [
                    'delivery_no', 'delivery_date', 'delivered_by', 'route', 'remarks', 'branch_code', 'receive_entered_by', 'receive_entered_on',
                    'receive_entered_on', 'delivery_entered_by', 'delivery_entered_date'
                ];
                foreach ($fillable as $f) {
                    if ($f == 'receive_entered_on') {
                        $m_data[$f] = null;
                    } elseif ($f == 'delivery_entered_by') {
                        $m_data[$f] = Auth::user()->id;
                    } else
                        $m_data[$f] = isset($request[$f]) ? $request[$f] : '';
                }
                $m_data['delivery_no'] = $delivery_no;
                $m_data['delivery_entered_date'] = date('Y-m-d');
                $m_data['branch_code'] = $this->admin_data['login_user']->branch_code;
                $m_data['remarks'] = isset($request->remarks) ? $request->remarks : '';
                $route_master = RouteDeliveryMaster::create($m_data);
                foreach ($delivery_information as $i => $b) {

                    $input = [
                        'master_id', 'master_id', 'manifest_no', 'bill_no', 'consignee_name', 'consignee_address', 'telephone_no', 'mobile_no',
                        'merchandise_code', 'weight', 'quantity', 'received_by', 'received_on', 'remarks', 'branch_code',
                        'rto', 'recent_date', 'sno'
                    ];
                    foreach ($input as $j) {
                        if ($j == 'telephone_no') {
                            $data2[$j] = isset($b->consignee_telephone_no) ? $b->consignee_telephone_no : '';
                        } elseif ($j == 'mobile_no') {
                            $data2[$j] = isset($b->consignee_mobile_no) ? $b->consignee_mobile_no : '';
                        } elseif ($j == 'received_on') {
                            $data2[$j] = null;
                        } elseif ($j == 'recent_date') {
                            $data2[$j] = date('Y-m-d');
                        } else {
                            $data2[$j] = isset($b->$j) ? $b->$j : '';
                        }
                    }
                    $data2['master_id'] = $route_master->id;
                    $data2['branch_code'] = $this->admin_data['login_user']->branch_code;
                    $data2['sno'] = $i + 1;
                    RouteDeliveryDetail::create($data2);
                    $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
                    $attribute = [
                        'tracking_id' => $tracking_id,
                        'track_date' => date('Y-m-d'),
                        'bill_no' => $b->bill_no,
                        'reference_no' => $delivery_no,
                        'status' => 'OUT FOR DELIVERY',
                        'activity' => 'OUT FOR DELIVERY',
                        'location' => getRouteName($m_data['route'],$this->admin_data['login_user']->branch_code),
                        'user_id' => $this->admin_data['login_user']->user_id,
                        'timestamp' => date('Y-m-d'),
                        'tag' => 'R',
                        'branch_code' => isset($b->location_from)?$b->location_from:$b->dest_location_code,
                        'crossing_no' => ''

                    ];
                    DB::table('booking_information')
                        ->where('bill_no', $b->bill_no)// find your user by their email
                        ->limit(1)// optional - to ensure only one record is updated.
                        ->update(array('delivery_no' => $delivery_no));
                    Tracking::create($attribute);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Session::flash('successMsg', $e->getMessage());
                dd($e->getMessage());
                // something went wrong
            }
            Session::flash('successMsg', 'Your delivery no is ' . $delivery_no);
            return response()->json(['success' => true, 'message' => 'Your delivery no is ' . $delivery_no, 'data' => ['delivery_no' => $delivery_no]], 200);
        }
    }

    public function branchDeliveryIndex()
    {
        Session::forget('branch_delivery_information');
        $this->admin_data['manifest_datas'] = ManifestMaster::orderBy('id', 'desc')->get();
        return view('admin.route_delivery.branch.index', $this->admin_data);
    }

    public function branchAddBill(Request $request)
    {
        //        if($tag == "Y")
//        {
//            $tempBillNo = $api_data->tempBillNo;
//            $query = "SELECT BILL_NO, CONSIGNEE_CODE, CONSIGNEE_NAME, CONSIGNEE_ADDRESS, TELEPHONE_NO, MOBILE_NO,
//						MERCHANDISE_TYPE, GET_MERCHANDISE_NAME(MERCHANDISE_CODE) MERCHANDISE, MERCHANDISE_CODE,
//						QUANTITY, WEIGHT, MANIFEST_NO, FISCAL_YEAR
//					FROM
//					(
//						SELECT BILL_NO, CONSIGNEE_CODE, CONSIGNEE_NAME, CONSIGNEE_ADDRESS, TELEPHONE_NO, MOBILE_NO,
//							MERCHANDISE_TYPE, MERCHANDISE_CODE, QUANTITY, WEIGHT, MANIFEST_NO, FISCAL_YEAR
//						FROM BKIN
//						WHERE BILL_NO = '$tempBillNo'
//							AND DEST_LOCATION_CODE IN (SELECT LOCATION_CODE FROM RP
//								WHERE BRANCH_CODE = '".$api_data->branchCode."')
//
//						UNION
//						SELECT BILL_NO, CONSIGNEE_CODE, CONSIGNEE_NAME, CONSIGNEE_ADDRESS, TELEPHONE_NO, MOBILE_NO,
//							MAND.MERCHANDISE_TYPE, MERCHANDISE_CODE, QUANTITY, WEIGHT, MANM.MANIFEST_NO, MANM.FISCAL_YEAR
//						FROM MANM, MAND
//						WHERE MANM.MANIFEST_NO = MAND.MANIFEST_NO
//							AND BILL_NO = '$tempBillNo'
//							AND NVL(CONSIGNEE_RECEIVE,'N') = 'N'
//							AND DELIVERY_TAKEN_BY IS NULL
//							AND DELIVERY_TAKEN_ON IS NULL
//							AND CONSIGNEE_RECEIVE_DATE IS NULL
//							AND NVL(RECEIVE_CONDITION,'N') = 'R'
//							AND MANIFEST_NO_MADE IS NULL
//							AND MAND.BRANCH_CODE <> '".$api_data->branchCode."'
//
//					)
//					ORDER BY CONSIGNEE_NAME";
//
//            $resDeliveryFetch = $conn->selectdatafromtable($c, $query);
        $add_bill = BookingInformation::join('route_para', 'route_para.location_code', '=', 'booking_information.dest_location_code')
            ->join('manifest_detail', 'manifest_detail.bill_no', '=', 'booking_information.bill_no')
            ->join('manifest_master', 'manifest_master.manifest_no', '=', 'manifest_detail.manifest_no')
            ->where('manifest_detail.delivery_taken_by', '=', '')
            ->whereNull('manifest_detail.delivery_taken_on')
            ->where('manifest_detail.consignee_receive', '=', '')
            ->whereNull('consignee_receive_date')
            ->where('manifest_master.received_by', '!=', '')
            ->where('booking_information.bill_no', '=', $request->bill_no)
            ->where('route_para.branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
        if ($add_bill == null) {
            return response()->json(['success' => false, 'message' => 'Bill mismatch or no bill found', 'data' => null], 200);
        }
        $branch_delivery_information = Session::get('branch_delivery_information');
        if (!isset($branch_delivery_information))
            $branch_delivery_information = new Collection();
//        $booking_information='';
        $push = 'Y';
        $add = (object)$add_bill->getAttributes();
        if (isset($branch_delivery_information)) {

            foreach ($branch_delivery_information as $b) {
                if ($b->bill_no == $request->bill_no) {
                    $push = 'N';
                }
            }
        }
        if ($push == 'Y') {
            $branch_delivery_information->push($add);
        }
        Session::put('branch_delivery_information', $branch_delivery_information);
        $add_bill_html = view('admin.route_delivery.branch.add_bill', compact('branch_delivery_information'))->render();
        return response()->json(['success' => true, 'message' => 'Bill no Successfully added', 'data' => ['add_bill_html' => $add_bill_html]], 200);
    }

    public function branchDeliveryStore(Request $request)
    {
        $branch_delivery_information = Session::get('branch_delivery_information');
        $query = RouteDeliveryMaster::select(DB::raw("MAX(CAST(SUBSTRING(delivery_no, 11) AS UNSIGNED)+1) AS new_delivery_no"))->first();
        if ($query->new_delivery_no != null) {

            $delivery_no = date('Y') . $this->admin_data['login_user']->branch_code . '-00' . $query->new_delivery_no;
        } else {
            $delivery_no = date('Y') . $this->admin_data['login_user']->branch_code . '-' . '001';
        }
        DB::beginTransaction();
        try {
            $fillable = [
                'delivery_no', 'delivery_date', 'delivered_by', 'route', 'remarks', 'branch_code', 'receive_entered_by', 'receive_entered_on',
                'receive_entered_on', 'delivery_entered_by', 'delivery_entered_date'
            ];
            foreach ($fillable as $f) {
                if ($f == 'receive_entered_on') {
                    $m_data[$f] = null;
                } elseif ($f == 'delivery_entered_by') {
                    $m_data[$f] = Auth::user()->id;
                } else
                    $m_data[$f] = isset($request[$f]) ? $request[$f] : '';
            }
            $m_data['delivery_no'] = $delivery_no;
            $m_data['delivery_entered_date'] = date('Y-m-d');
            $m_data['branch_code'] = $this->admin_data['login_user']->branch_code;
            $m_data['receive_entered_by'] = $this->admin_data['login_user']->employee_name;
            $m_data['receive_entered_on'] = date('Y-m-d');
            $route_master = RouteDeliveryMaster::create($m_data);
            foreach ($branch_delivery_information as $i => $b) {

                $input = [
                    'master_id', 'master_id', 'manifest_no', 'bill_no', 'consignee_name', 'consignee_address', 'telephone_no', 'mobile_no',
                    'merchandise_code', 'weight', 'quantity', 'received_by', 'received_on', 'remarks', 'branch_code',
                    'rto', 'recent_date', 'sno'
                ];
                foreach ($input as $j) {
                    if ($j == 'telephone_no') {
                        $data2[$j] = isset($b->consignee_telephone_no) ? $b->consignee_telephone_no : '';
                    } elseif ($j == 'mobile_no') {
                        $data2[$j] = isset($b->consignee_mobile_no) ? $b->consignee_mobile_no : '';
                    } elseif ($j == 'recent_date') {
                        $data2[$j] = date('Y-m-d');
                    } elseif ($j == 'received_by') {
                        $data2[$j] = $request[$j][$i];
                    } elseif ($j == 'received_on') {
                        $data2[$j] = $request[$j][$i];
                    } else {
                        $data2[$j] = isset($b->$j) ? $b->$j : '';
                    }
                }
                $data2['master_id'] = $route_master->id;
                $data2['branch_code'] = $this->admin_data['login_user']->branch_code;
                $data2['sno'] = $i + 1;
                RouteDeliveryDetail::create($data2);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('successMsg', $e->getMessage());
            dd($e->getMessage());
            // something went wrong
        }
        Session::flash('successMsg', 'Your delivery no is ' . $delivery_no);
        return response()->json(['success' => true, 'message' => 'Your delivery no is ' . $delivery_no, 'data' => null], 200);

    }
}
