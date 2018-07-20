<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BillStockDetail;
use App\Models\Admin\BookingInformation;
use App\Models\Admin\BranchPara;
use App\Models\Admin\Company;
use App\Models\Admin\CountryPara;
use App\Models\Admin\CustomerPara;
use App\Models\Admin\EmployeeParameter;
use App\Models\Admin\LocationHierarachy;
use App\Models\Admin\MerchandisePara;
use App\Models\Admin\PickupDetail;
use App\Models\Admin\PickupInformation;
use App\Models\Admin\RoutePara;
use App\Models\Admin\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\App;

class PickupController extends DashboardController
{
    public $admin_data;

    public function __construct(BookingInformation $booking)
    {
        $this->booking = $booking;

        parent::__construct();
    }

    public function index()
    {
        $this->admin_data['branch_para']=BranchPara::where('branch_code','=',$this->admin_data['login_user']->branch_code)->first();
        $this->admin_data['route_para'] = RoutePara::select('route_para.route_code','route_para.route_name')
            ->join('branch_paras as b','b.branch_code','=','route_para.branch_code')
            ->where([
            ['b.group_code', '=',  $this->admin_data['branch_para']->group_code],
            ['route_para.pickup_tag', '=', 'Y']
        ])->groupBy('route_para.route_name','route_para.route_code')->get();
        $this->admin_data['pickup_info'] = PickupInformation::select('pickup_information.*', 'u1.name as pickedup_by', 'u2.name as entered_by')
            ->join('users as u1', 'u1.id', '=', 'pickup_information.pickedup_by')
            ->join('users as u2', 'u2.id', '=', 'pickup_information.entered_by')
            ->orderBy('id', 'desc')->get();
        $this->admin_data['employee'] = CustomerPara::orderBy('id', 'desc')->get();
        $this->admin_data['country_para'] = CountryPara::   orderBy('country_name', 'desc')->get();
        $this->admin_data['merchandises'] = MerchandisePara::orderBy('id', 'desc')->get();
        $this->admin_data['customer'] =CustomerPara::where('used_tag','=','Y')->join('branch_paras as b','b.branch_code','=','customer_para.branch_code')->where('b.group_code','=',$this->admin_data['branch_para']->group_code)->orderBy('customer_para.shipper_name', 'asc')->get();;
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->where('location_type','=','PLC')->get();
        $this->admin_data['employees'] = EmployeeParameter::join('users', 'users.id', '=', 'employee_information_para.user_id')
            ->join('branch_paras as b','b.branch_code','=','employee_information_para.branch_code')
             ->where('b.group_code','=',$this->admin_data['branch_para']->group_code)
            ->orderBy('employee_information_para.id', 'desc')->get();
        return view('admin.pickup.index', $this->admin_data);
    }

    public function store(Request $request)
    {
        if($request->pickup_code !=null){
            return response()->json(['success' => false, 'message' => 'Already saved'], 200);
        }
        $query = PickupInformation::select(DB::raw("MAX(CAST(SUBSTRING(pickup_code, 11) AS UNSIGNED)+1) AS pickup_code"))->first();
        if ($query->pickup_code != null) {
            $pickup_code = date('Y') . $this->admin_data['login_user']->branch_code . '-00' . $query->pickup_code;
        } else {
            $pickup_code = date('Y') . $this->admin_data['login_user']->branch_code . '-' . '001';
        }
        DB::beginTransaction();
        try {
            $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
            $bill_no = '';
            PickupInformation::create(['pickup_code' => $pickup_code, 'pickedup_by' => $request->pickedup_by, 'pickup_date' => $request->book_date, 'route' => $request->route_name, 'entered_by' => Auth::user()->id, 'branch_code' => $this->admin_data['login_user']->branch_code, 'fiscal_year' => date('Y')]);
            for ($i = 0; $i < count($request->shipper_code); $i++) {
                $data = $request->except(['_token', 'book_date', 'phone', 'route_name', 'pickedup_by', 'sn_0']);
                $issue_data = $this->booking->generateBill(['fiscal_year' => date('Y'), 'branch_code' => $this->admin_data['login_user']->branch_code]);
                if(!$issue_data)
                    return response()->json(['success' => false, 'message' => 'Please check the bill issue'], 200);
                $input = ['pickup_code', 'shipper_code', 'consignee_name', 'consignee_address',
                    'location_code', 'mobile_no', 'telephone_no', 'merchandise_code',
                    'mailing_mode', 'quantity', 'weight', 'description', 'bill_no', 'book_tag',
                    'branch_code', 'fiscal_year', 'crossing_no', 'SN'
                ];
                foreach ($input as $j) {
                    $data2[$j] = isset($data[$j][$i]) ? $data[$j][$i] : '';
                }
                $data2['pickup_code'] = $pickup_code;
                $data2['SN'] = $i + 1;
                $data2['branch_code'] = $this->admin_data['login_user']->branch_code;
                $data2['bill_no'] = $issue_data->bill_no;
                $data2['book_tag'] = 'Y';
                PickupDetail::create($data2);
                $input = [
                    'bill_no', 'book_date', 'shipper_code', 'sender_name', 'sender_number', 'consignee_name', 'consignee_address', 'dest_location_code', 'org_location_code', 'consignee_telephone_no',
                    'consignee_mobile_no', 'merchandise_code', 'mailing_mode', 'quantity', 'weight', 'description', 'payment_mode',  'voucher_no',
                    'voucher_code', 'cheque_no', 'transaction_id', 'prepared_by', 'prepared_on', 'branch_code', 'manifest_no', 'zone_code', 'export_tag',
                    'delivery_no', 'crossing_no', 'cod_amount','statement_no'
                ];
                foreach ($input as $k) {
                    if ($k == 'cod_amount') {
                        $data3[$k] = isset($data[$k][$i]) ? $data[$k][$i] : 0;
                    } else {
                        $data3[$k] = isset($data[$k][$i]) ? $data[$k][$i] : '';
                    }
                }
                $data3['bill_no'] = $issue_data->bill_no;
                $data3['branch_code'] = $this->admin_data['login_user']->branch_code;
                $data3['prepared_by'] = Auth::user()->id;
                $data3['prepared_on'] = date('Y-m-d');
                $data3['book_date']=$request->book_date;
                $data3['dest_location_code']=$data['location_code'][$i];
                $data3['payment_mode']='Credit';
                $branch = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
                $data3['org_location_code'] = $branch->group_code;
                BookingInformation::create($data3);
                $attribute = [
                    'tracking_id' => $tracking_id,
                    'track_date' => date('Y-m-d'),
                    'bill_no' => $issue_data->bill_no,
                    'reference_no' => $issue_data->bill_no,
                    'status' => 'SHIPMENT BOOKED',
                    'activity' => 'SHIPMENT BOOKED',
                    'location' => '',
                    'user_id' => $this->admin_data['login_user']->user_id,
                    'timestamp' => date('Y-m-d'),
                    'crossing_no' => '',
                    'tag' => 'B',
                    'branch_code' => $this->admin_data['login_user']->branch_code,
                    'crossing_no' => ''

                ];
                Tracking::create($attribute);

                BillStockDetail::create(['issue_id' => $issue_data->issue_id, 'bill_no' => $issue_data->bill_no, 'used_tag' => 'Y', 'branch_code' => $this->admin_data['login_user']->branch_code, 'bill_type' => 'A', 'fiscal_year' => '2074/2075']);
                $bill_no .= $issue_data->bill_no . ',';
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('successMsg', $e->getMessage());
            dd($e->getMessage());
            // something went wrong
        }
        $pickup_details=PickupDetail::select('c.shipper_name as shipper_name','pickup_detail.*')->where('pickup_code','=',$pickup_code)->join('customer_para as c','c.shipper_code','=','pickup_detail.shipper_code')->orderBy('pickup_detail.id','asc')->get();
    $bill_table=view('admin.pickup.bill_html',compact('pickup_details'))->render();
        Session::flash('successMsg', 'Bill NO ' . $bill_no . ' generated Successfully and your pick up code is ' . $pickup_code);
        return response()->json(['success' => true, 'message' => 'Bill NO ' . $bill_no . ' generated Successfully and booking information saved successfully','data'=>['pickup_code'=>$pickup_code,'bill_table'=>$bill_table]], 200);
    }

    public function edit($id)
    {
        $this->admin_data['pickup_info'] = PickupInformation::find($id);
        $this->admin_data['pickup_detail'] = PickupDetail::select('pickup_detail.*')
            ->where('pickup_code', '=', $this->admin_data['pickup_info']->pickup_code)
            ->get();
        $this->admin_data['route_para'] = RoutePara::where([
            ['branch_code', '=', $this->admin_data['login_user']->branch_code],
            ['pickup_tag', '=', 'Y']
        ])->distinct()->get();
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->get();
        $this->admin_data['merchandises'] = MerchandisePara::orderBy('id', 'desc')->get();

        $this->admin_data['employees'] = EmployeeParameter::join('users', 'users.id', '=', 'employee_information_para.user_id')->orderBy('employee_information_para.id', 'desc')->get();

        return view('admin.pickup.edit', $this->admin_data);

    }
    public function podPdf(Request $request){

        $this->admin_data['query']=PickupDetail::select('b.*','pickup_detail.*')
            ->where('pickup_detail.pickup_code', '=', $request->pickup_code)
            ->join('booking_information as b','b.bill_no', '=', 'pickup_detail.bill_no')
            ->get();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.pod.podPdf', $this->admin_data)->render());
        $customPaper = array(0,0,595,421);
        return $pdf->setPaper($customPaper)->stream();
    }

    public function delete($id)
    {
        $pickup_bill = PickupDetail::find($id);
        $pickup_bill->delete();

        session::flash('successMsg', 'Bill No. Deleted Successfully');
        return response()->json(['success' => true, 'message' => 'Bill no. deleted Successfully', 'data' => null], 200);

    }

    public function deleteBooking($id)
    {
        $pickup_info = PickupInformation::find($id);
        PickupInformation::where('id', '=', $id)->delete();
        PickupDetail::where('pickup_code', '=', $pickup_info->pickup_code)->delete();
        session::flash('successMsg', 'Pickup information Deleted Successfully');
        return response()->json(['success' => true, 'message' => 'Pickup information deleted Successfully', 'data' => null], 200);

    }


}
