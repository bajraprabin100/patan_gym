<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BillStockDetail;
use App\Models\Admin\BillStockPara;
use App\Models\Admin\BookingInformation;
use App\Models\Admin\BranchPara;
use App\Models\Admin\Company;
use App\Models\Admin\CountryPara;
use App\Models\Admin\CustomerPara;
use App\Models\Admin\EmployeeParameter;
use App\Models\Admin\LocationHierarachy;
use App\Models\Admin\ManifestDetail;
use App\Models\Admin\ManifestMaster;
use App\Models\Admin\MerchandisePara;
use App\Models\Admin\PickupDetail;
use App\Models\Admin\PickupInformation;
use App\Models\Admin\RoutePara;
use App\Models\Admin\Tracking;
use App\Repositories\Backend\BranchPara\BranchParaInterface;
use App\Repositories\Backend\BranchPara\ReportInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Session;
use App\Models\Admin\GroupCode;


class BranchManifestController extends DashboardController
{
    public $admin_data;
    public $branch_para;

    public function __construct(BranchParaInterface $branch_para)
    {

        parent::__construct();

    }

    public function index()
    {
        Session::forget('branch_manifest');
        $this->admin_data['branch_para'] = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
        $this->admin_data['group_data'] = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->get();
        $this->admin_data['route_para'] = RoutePara::where([
            ['branch_code', '=', $this->admin_data['login_user']->branch_code],
            ['pickup_tag', '=', 'Y']
        ])->distinct()->get();
        $this->admin_data['pickup_info'] = PickupInformation::select('pickup_information.*', 'u1.name as pickedup_by', 'u2.name as entered_by')
            ->join('users as u1', 'u1.id', '=', 'pickup_information.pickedup_by')
            ->join('users as u2', 'u2.id', '=', 'pickup_information.entered_by')
            ->orderBy('id', 'desc')->get();
        $this->admin_data['employee'] = CustomerPara::orderBy('id', 'desc')->get();
        $this->admin_data['country_para'] = CountryPara::   orderBy('country_name', 'desc')->get();
        $this->admin_data['merchandises'] = MerchandisePara::orderBy('id', 'desc')->get();
        $this->admin_data['customer'] = CustomerPara::orderBy('id', 'desc')->get();
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->where('location_type', '=', 'PLC')->get();;
        $this->admin_data['employees'] = EmployeeParameter::join('users', 'users.id', '=', 'employee_information_para.user_id')->orderBy('employee_information_para.id', 'desc')->get();
        return view('admin.branch_manifest.index', $this->admin_data);
    }

    public function getBillAddress(Request $request)
    {
//        dd($request->bill_no);
//        $check_issue_bill=BillStockPara::where('bill_no_to', '<=', $request->bill_no)
//            ->where('bill_no_from', '>=',  $request->bill_no)
//            ->first();
        $check_issue_bill = BillStockPara::whereRaw('bill_no_to >= ' . $request->bill_no)
            ->whereRaw('bill_no_from <= ' . $request->bill_no)
            ->where('bill_type', '=', 'Manual')->first();
        $success = true;
        $data = ['branch_code' => isset($check_issue_bill->branch_code) ? $check_issue_bill->branch_code : ''];
        $message = 'Bill found';
        if (!$check_issue_bill) {
            $success = false;
            $message = 'No bill issuer found';
            $data = null;
        }
        $check_bill_issued = BillStockDetail::where('bill_no', '=', $request->bill_no)->where('bill_type', '=', 'Manual')->first();
        if ($check_bill_issued) {
            $success = false;
            $message = 'Bill already been used';
            $data = null;
        }

        return response()->json(['success' => $success, 'message' => $message, 'data' => $data], 200);

    }

    public function store(Request $request)
    {
        if($request->manifest_no==null) {
            $query = ManifestMaster::select(DB::raw("MAX(CAST(SUBSTRING(manifest_no, 11) AS UNSIGNED)+1) AS new_manifest_no"))->first();
            if ($query->new_manifest_no != null) {

                $manifest_no = date('Y') . $this->admin_data['login_user']->branch_code . '-00' . $query->new_manifest_no;
            } else {
                $manifest_no = date('Y') . $this->admin_data['login_user']->branch_code . '-' . '001';
            }
//        $booking_information = Session::get('booking_information');
            DB::beginTransaction();
            try {
                $bill_no = '';
                $branch = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
                ManifestMaster::create(['manifest_no' => $manifest_no, 'manifest_date' => $request->manifest_date, 'location_from' => '', 'location_to' => $this->admin_data['login_user']->branch_code, 'remarks' => '', 'prepared_by' => Auth::user()->id, 'prepared_on' => date('Y-m-d'), 'received_by' => Auth::user()->id, 'received_on' => date('Y-m-d'), 'export_tag' => '', 'branch_code' => $this->admin_data['login_user']->branch_code, 'posted_date' => null, 'receive_branch' => null, 'receive_timestamp' => null, 'type' => 'manual_bill']);
                foreach ($request['bill_no'] as $i => $b) {

                    $input = [
                        'manifest_no', 'bill_no', 'shipper_code', 'consignee_name', 'consignee_address', 'merchandise_code', 'quantity', 'weight', 'receive_condition', 'consignee_receive', 'consignee_receive_date', 'remarks', 'branch_code',
                        'delivery_taken_by', 'delivery_taken_on', 'manifest_no_made', 'rto', 'fiscal_year', 'manifest_no_rto', 'location_from', 'location_to'
                    ];
                    foreach ($input as $j) {

                        if ($j == 'consignee_receive_date' || $j == 'delivery_taken_on') {
                            $data2[$j] = null;
                        } else {
                            $data2[$j] = isset($request[$j][$i]) ? $request[$j][$i] : '';
                        }
                    }
                    $data2['manifest_no'] = $manifest_no;
                    $data2['branch_code'] = $this->admin_data['login_user']->branch_code;
                    $data2['receive_condition'] = 'R';
                    $location=LocationHierarachy::where('location_code','=',$data2['location_to'])->first();
                    $data2['consignee_address']=$location->location_name;
                    ManifestDetail::create($data2);
                    $issue_data = BillStockPara::where([
                        ['bill_no_from', '<=', $data2['bill_no']],
                        ['bill_no_to', '>=', $data2['bill_no']]
                    ])->first();
                    BillStockDetail::create(['issue_id' => $issue_data->issue_id, 'bill_no' => $data2['bill_no'], 'used_tag' => 'Y', 'branch_code' => $this->admin_data['login_user']->branch_code, 'bill_type' => 'Manual', 'fiscal_year' => '2074/2075']);
                    $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
                    $attribute = [
                        'tracking_id' => $tracking_id,
                        'track_date' => date('Y-m-d'),
                        'bill_no' => $data2['bill_no'],
                        'reference_no' => $data2['manifest_no'],
                        'status' => 'ARRIVED HUB',
                        'activity' => 'ARRIVED HUB',
                        'location' => $branch->branch_name,
                        'user_id' => $this->admin_data['login_user']->user_id,
                        'timestamp' => date('Y-m-d'),
                        'tag' => 'MR',
                        'branch_code' => $data2['location_from'],
                        'crossing_no' => ''

                    ];
                    $check_track = Tracking::where([
                        ['reference_no', '=', $data2['manifest_no']],
                        ['bill_no', '=', $data2['bill_no']],
                        ['status', '=', 'ARRIVED HUB']
                    ])->first();
                    if (!$check_track)
                        Tracking::create($attribute);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Session::flash('successMsg', $e->getMessage());
                dd($e->getMessage());
                // something went wrong
            }
            Session::flash('successMsg', 'Your manifest no is ' . $manifest_no);
            return response()->json(['success' => true, 'message' => 'Your manifest no is ' . $manifest_no, 'data' => ['manifest_no' => $manifest_no]], 200);
        }
    }
}
