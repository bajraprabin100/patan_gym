<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BillStockDetail;
use App\Models\Admin\BookingInformation;
use App\Models\Admin\BranchPara;
use App\Models\Admin\Company;
use App\Models\Admin\CountryPara;
use App\Models\Admin\CustomerPara;
use App\Models\Admin\GroupCode;
use App\Models\Admin\ManifestMaster;
use App\Models\Admin\MerchandisePara;
use App\Models\Admin\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\Admin\LocationHierarachy;
use Illuminate\Support\Facades\App;
use Input;
use Excel;
use DB;

class BookingController extends DashboardController
{
    public $admin_data;
    public $booking;

    public function __construct(BookingInformation $booking)
    {
        $this->booking = $booking;
        parent::__construct();
    }

    public function index()
    {
        $branch_para=BranchPara::where('branch_code','=',$this->admin_data['login_user']->branch_code)->first();
        $this->admin_data['customer_paras'] = CustomerPara::where('used_tag','=','Y')->join('branch_paras as b','b.branch_code','=','customer_para.branch_code')->where('b.group_code','=',$branch_para->group_code)->orderBy('customer_para.id', 'desc')->get();
        $this->admin_data['master_companies'] = Company::all();
        $this->admin_data['country_para'] = CountryPara::orderBy('country_name', 'desc')->get();
        $this->admin_data['merchandises'] = MerchandisePara::orderBy('id', 'desc')->get();
        $this->admin_data['customer'] = CustomerPara::where('used_tag','=','Y')
            ->join('branch_paras as b','b.branch_code','=','customer_para.branch_code')
            ->where('b.group_code','=',$branch_para->group_code)
            ->orderBy('customer_para.shipper_name', 'asc')->get();
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')
            ->where('location_type', '!=', 'DIS')
            ->where('location_type', '!=', 'ZON')
            ->get();
        $this->admin_data['booking_details'] = BookingInformation::select('booking_information.*', 'customer_para.customer_name', 'customer_para.address', 'customer_para.phone')
            ->join('customer_para', 'customer_para.shipper_code', '=', 'booking_information.shipper_code')
            ->orderBy('shipper_name', 'desc')->get();
        return view('admin.booking_information.index', $this->admin_data);
    }
    public function cnPdf(Request $request){
        $this->admin_data['q'] = BookingInformation::select('booking_information.*', 'customer_para.*','u.name as name')
            ->where('booking_information.bill_no','=', $request->bill_no)
            ->join('customer_para', 'customer_para.shipper_code', '=', 'booking_information.shipper_code')
            ->where('booking_information.branch_code', $this->admin_data['login_user']->branch_code)
            ->orderBy('booking_information.bill_no', 'asc')
            ->leftJoin('users as u','u.id','=','booking_information.prepared_by')
            ->first();

//        dd( $this->admin_data['query'] );
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.booking_information.cnpdf', $this->admin_data)->render());
        $customPaper = array(0,0,595,421);
        return $pdf->setPaper($customPaper)->stream();
    }
    public function podPdf(Request $request){
        $this->admin_data['q'] = BookingInformation::select('booking_information.*', 'customer_para.*')
            ->where('booking_information.bill_no','=', $request->bill_no)
            ->join('customer_para', 'customer_para.shipper_code', '=', 'booking_information.shipper_code')
            ->where('booking_information.branch_code', $this->admin_data['login_user']->branch_code)

            ->orderBy('booking_information.bill_no', 'asc')
            ->first();

//        dd( $this->admin_data['query'] );
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.booking_information.podPDF', $this->admin_data)->render());
        $customPaper = array(0,0,595,421);
        return $pdf->setPaper($customPaper)->stream();
    }
    public function cnpodPdf(Request $request){
        $this->admin_data['q'] = BookingInformation::select('booking_information.*', 'customer_para.*')
            ->where('booking_information.bill_no','=', $request->bill_no)
            ->join('customer_para', 'customer_para.shipper_code', '=', 'booking_information.shipper_code')
            ->where('booking_information.branch_code', $this->admin_data['login_user']->branch_code)
            ->orderBy('booking_information.bill_no', 'asc')
            ->first();

//        dd( $this->admin_data['query'] );
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.booking_information.cnpodPdf', $this->admin_data)->render());
        $customPaper = array(0,0,595,421);
        return $pdf->setPaper($customPaper)->stream();
    }

    public function getVolumeWeight(Request $request)
    {
        $volumetric_weight = ($request['length'] * $request['breadth'] * $request['height']) / 5000;
        $total_volume=$volumetric_weight*$request->quantity;
        return response()->json(['success' => true, 'message' => 'Calculated successfullly', 'data' => $total_volume], 200);

    }

    public function customerCodCheck(Request $request)
    {
        $customer = CustomerPara::where('shipper_code', '=', $request->shipper_code)
            ->where('cod', '=', 'Yes')->first();
        $customer_type = CustomerPara::where('shipper_code', '=', $request->shipper_code)->first();
        if (!$customer)
            return response()->json(['success' => true, 'message' => 'COD not enabled customer', 'data' => ['cod' => false, 'payment_type' => $customer_type->payment_type]], 200);
        $cod_branch = BranchPara::where('branch_code', '=', $customer->branch_code)
            ->where('vat_applicable', '=', 'Yes')->first();
        if (!$cod_branch) {
            return response()->json(['success' => true, 'message' => 'COD not enabled customer', 'data' => ['cod' => false, 'payment_type' => $customer->payment_type]], 200);
        }
        return response()->json(['success' => true, 'message' => 'COD  enabled customer', 'data' => ['cod' => true, 'payment_type' => $customer->payment_type]], 200);

    }

    public function store(Request $request)
    {


        $bill_check = BookingInformation::where('bill_no', '=', $request->bill_no)->first();
        $data = $request->except('_token', 'book_date_bs', 'volume_weight');
        if (!$bill_check) {
            $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
            $current_year = date('Y');
//        dd($current_year);

            $issue_data = $this->booking->generateBill(['fiscal_year' => $current_year, 'branch_code' => $this->admin_data['login_user']->branch_code]);
            $data['bill_no'] = $issue_data->bill_no;
            $data['branch_code'] = $this->admin_data['login_user']->branch_code;
            $data['prepared_by'] = Auth::user()->id;
            $data['prepared_on'] = date('Y-m-d');
            $input = [
                'bill_no', 'book_date', 'shipper_code', 'sender_name', 'sender_number', 'consignee_name', 'consignee_address', 'dest_location_code', 'org_location_code', 'consignee_telephone_no',
                'consignee_mobile_no', 'merchandise_code', 'mailing_mode', 'quantity', 'weight', 'description', 'payment_mode', 'weight_charge', 'other_charge', 'declared_value', 'voucher_no',
                'voucher_code', 'cheque_no', 'transaction_id', 'prepared_by', 'prepared_on', 'branch_code', 'manifest_no', 'amount', 'total_amount', 'statement_no', 'zone_code', 'export_tag',
                'delivery_no', 'crossing_no', 'length', 'breadth', 'height', 'cod_amount'
            ];
            foreach ($input as $i) {
                if ($i == 'cod_amount' || $i == 'weight_charge' || $i=='other_charge' || $i=='length' || $i=='breadth' || $i=='height') {
                    $data[$i] = isset($data[$i]) ? $data[$i] : 0;
                } else {
                    $data[$i] = isset($data[$i]) ? $data[$i] : '';
                }
            }
            $data['taxable_amount'] = $request->weight_charge + $request->other_charge;
            $data['vat'] = $data['taxable_amount'] * 13 / 100;
            $data['total_amount'] = $data['taxable_amount'] + $data['vat'];
            $branch = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
            $data['org_location_code'] = $branch->group_code;
            BookingInformation::create($data);
            BillStockDetail::create(['issue_id' => $issue_data->issue_id, 'bill_no' => $issue_data->bill_no, 'used_tag' => 'Y', 'branch_code' => $this->admin_data['login_user']->branch_code, 'bill_type' => 'A', 'fiscal_year' => '2074/2075']);
            $attribute = [
                'tracking_id' => $tracking_id,
                'track_date' => date('Y-m-d'),
                'bill_no' => $issue_data->bill_no,
                'reference_no' => $issue_data->bill_no,
                'status' => 'SHIPMENT BOOKED',
                'activity' => 'SHIPMENT BOOKED',
                'location' => $branch->branch_name,
                'user_id' => $this->admin_data['login_user']->user_id,
                'timestamp' => date('Y-m-d'),
                'crossing_no' => '',
                'tag' => 'B',
                'branch_code' => $this->admin_data['login_user']->branch_code,
                'crossing_no' => ''

            ];
            Tracking::create($attribute);
            $copy_field = [
                'shipper_code' => $request->shipper_code,
                'consignee_name' => $request->consignee_name,
                'mailing_mode' => $request->mailing_mode,
                'merchandise_code' => $request->merchandise_code,
                'description' => $request->description

            ];
            Session::put('copy_field', $copy_field);
            Session::put('sum_amount', $request->total_amount);
        } else {
            $input = [
                'bill_no', 'book_date', 'shipper_code', 'sender_name', 'sender_number', 'consignee_name', 'consignee_address', 'dest_location_code', 'org_location_code', 'consignee_telephone_no',
                'consignee_mobile_no', 'merchandise_code', 'mailing_mode', 'quantity', 'weight', 'description', 'payment_mode', 'weight_charge', 'other_charge', 'declared_value', 'voucher_no',
                'voucher_code', 'cheque_no', 'transaction_id', 'branch_code', 'manifest_no', 'amount', 'total_amount', 'statement_no', 'zone_code', 'export_tag',
                'delivery_no', 'crossing_no', 'length', 'breadth', 'height', 'cod_amount'
            ];
            foreach ($input as $i) {
                if ($i == 'cod_amount') {
                    $data[$i] = isset($data[$i]) ? $data[$i] : 0;
                } else {
                    $data[$i] = isset($data[$i]) ? $data[$i] : '';
                }
            }
            $data['taxable_amount'] = $request->weight_charge + $request->other_charge;
            $data['vat'] = $data['taxable_amount'] * 13 / 100;
            $data['total_amount'] = $data['taxable_amount'] + $data['vat'];
            BookingInformation::where('bill_no', '=', $request->bill_no)->update($data);
        }
        Session::flash('successMsg', 'Bill NO ' . $data['bill_no'] . ' generated Successfully and booking information saved successfully');

        return response()->json(['success' => true, 'message' => $data['bill_no'] . 'generated Successfully and booking information saved successfully', 'data' => ['bill_no' => $data['bill_no']]], 200);


    }

    public function billQuery(Request $request)
    {
        $this->admin_data['booking'] = BookingInformation::where('bill_no', '=', $request->bill_no)->first();

        if (!$this->admin_data['booking']) {
            return response()->json(['success' => false, 'message' => 'No bill found', 'data' => null], 200);
        }
        $this->admin_data['booking']->book_date_bs = english_to_nepali($this->admin_data['booking']->book_date)['total_date'];
//        $this->admin_data['customer_paras'] = CustomerPara::orderBy('id', 'desc')->get();
//        $this->admin_data['master_companies'] = Company::all();
//        $this->admin_data['country_para'] = CountryPara::orderBy('country_name', 'desc')->get();
//        $this->admin_data['merchandises'] = MerchandisePara::orderBy('id', 'desc')->get();
//        $this->admin_data['customer'] = CustomerPara::orderBy('id', 'desc')->get();
//        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->where('location_type','!=','DIS')->get();
//        $booking_html=view('admin.booking_information.bill_query_html',$this->admin_data)->render();

        return response()->json(['success' => true, 'message' => 'Successfully found bill no', 'data' => ['booking' => $this->admin_data['booking']]], 200);
    }

    public function focusName(Request $request)
    {

        $copy_field = Session::get('copy_field');
        return response()->json(['success' => true, 'message' => 'Copy field', 'data' => ['field' => $copy_field[$request->focus_name]]], 200);

    }

    public function edit($id)
    {
        $branch_para=BranchPara::where('branch_code','=',$this->admin_data['login_user']->branch_code)->first();
        $this->admin_data['booking_info'] = BookingInformation::where('bill_no','=',$id)->first();
        if(!$this->admin_data['booking_info'])
            return 'NO bill found or bill may be deleted';
        $this->admin_data['customer'] = CustomerPara::where('used_tag','=','Y')
            ->join('branch_paras as b','b.branch_code','=','customer_para.branch_code')
            ->where('b.group_code','=',$branch_para->group_code)
            ->orderBy('customer_para.shipper_name', 'asc')->get();
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->get();
        $this->admin_data['merchandises'] = MerchandisePara::orderBy('id', 'desc')->get();

        return view('admin.booking_information.ajaxEdit', $this->admin_data);
    }

    public function update(Request $request, $id)
    {
        $booking = BookingInformation::find($id);
        $booking->shipper_code = $request->shipper_code;
        $booking->consignee_name = $request->consignee_name;
        $booking->dest_location_code = $request->dest_location_code;
        $booking->consignee_address = $request->consignee_address;
        $booking->consignee_telephone_no = $request->consignee_telephone_no;
        $booking->consignee_mobile_no = $request->consignee_mobile_no;
        $booking->crossing_no = isset($request->crossing_no) ? $request->crossing_no : '';
        $booking->description = $request->description;
        $booking->merchandise_code = $request->merchandise_code;
        $booking->mailing_mode = $request->mailing_mode;
        $booking->weight = $request->weight;
        $booking->quantity = $request->quantity;
        $booking->payment_mode = $request->payment_mode;
        $booking->length = $request->length;
        $booking->breadth = $request->breadth;
        $booking->height = $request->height;
        $booking->weight_charge = $request->weight_charge;
        $booking->other_charge = $request->other_charge;
        $booking->amount = $request->amount;
        $booking->declared_value = $request->declared_value;
        $booking->total_amount = $request->total_amount;
        $booking->cod_amount = isset($request->cod_amount) ? $request->cod_amount : 0;
        $booking->save();
        Session::flash('successMsg', 'Booking Parameter has been updated successfully');
        return redirect()->back();
    }

    public function sumAmount(Request $request)
    {
        $sum_amount = Session::get('sum_amount');
        $sum = $sum_amount + $request->amount;
        return response()->json(['success' => true, 'message' => 'Added', 'data' => ['sum' => $sum]], 200);
    }

    public function destroy(Request $request, $id)
    {
        $booking = BookingInformation::find($id);
        $booking->delete();
        Session::flash('successMsg', 'Booking Parameter has been deleted successfully');
        return response()->json(['success' => true, 'message' => 'Booking Parameter has been deleted successfully'], 200);
    }

    public function excelBooking()
    {
        $this->admin_data['customers'] = CustomerPara::where("branch_code", '=', $this->admin_data['login_user']->branch_code)->get();
//        dd($this->admin_data['customers']);
        return view('admin.booking_information.excel', $this->admin_data);
    }

    public function getDetails(Request $request)
    {
        $request_data = $request->all();
        $path = Input::file('import_file')->getRealPath();
        $data = Excel::load($path, function ($reader) {
        })->get();
        $e_detail_html = view('admin.booking_information.excel_details', compact('data', 'request_data'))->render();
//        importCsv($data, 'branch_paras');
        return response()->json(['success' => true, 'message' => 'Excel Details added', 'data' => ['e_detail_html' => $e_detail_html]], 200);
    }

    public function excelStore(Request $request)
    {
        $data = $request->all();
        for ($j = 0; $j < count($data['book_date']); $j++) {
            $current_year = date('Y');
//        dd($current_year);
            $issue_data = $this->booking->generateBill(['fiscal_year' => $current_year, 'branch_code' => $this->admin_data['login_user']->branch_code]);

            $input = [
                'bill_no', 'book_date', 'shipper_code', 'sender_name', 'sender_number', 'consignee_name', 'consignee_address', 'dest_location_code', 'org_location_code', 'consignee_telephone_no',
                'consignee_mobile_no', 'merchandise_code', 'mailing_mode', 'quantity', 'weight', 'description', 'payment_mode', 'weight_charge', 'other_charge', 'declared_value', 'voucher_no',
                'voucher_code', 'cheque_no', 'transaction_id', 'prepared_by', 'prepared_on', 'branch_code', 'manifest_no', 'amount', 'total_amount', 'statement_no', 'zone_code', 'export_tag',
                'delivery_no', 'crossing_no', 'length', 'breadth', 'height', 'cod_amount'
            ];
            foreach ($input as $i) {
                if ($i == 'cod_amount') {
                    $data_store[$i] = isset($data[$i][$j]) ? $data[$i][$j] : 0;
                } else {
                    $data_store[$i] = isset($data[$i][$j]) ? $data[$i][$j] : '';
                }

            }
            $data_store['bill_no'] = $issue_data->bill_no;
            $data_store['branch_code'] = $this->admin_data['login_user']->branch_code;
            $data_store['prepared_by'] = Auth::user()->id;
            $data_store['prepared_on'] = date('Y-m-d');
            $bill_no[$j] = $issue_data->bill_no;
            BookingInformation::create($data_store);
            BillStockDetail::create(['issue_id' => $issue_data->issue_id, 'bill_no' => $issue_data->bill_no, 'used_tag' => 'Y', 'branch_code' => $this->admin_data['login_user']->branch_code, 'bill_type' => 'A', 'fiscal_year' => '2074/2075']);
            $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
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
                'tag' => 'B',
                'branch_code' => $this->admin_data['login_user']->branch_code,
                'crossing_no' => ''

            ];
            Tracking::create($attribute);
        }
        $e_detail_html = view('admin.booking_information.save_excel_details', compact('data', 'bill_no'))->render();
        Session::flash('successMsg', 'Bill NO generated Successfully and booking information saved successfully');
        return response()->json(['success' => true, 'message' => 'Bill NO generated Successfully and booking information saved successfully', 'data' => ['e_detail_html' => $e_detail_html]], 200);
    }
}
