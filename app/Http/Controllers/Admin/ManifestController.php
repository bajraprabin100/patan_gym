<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BookingInformation;
use App\Models\Admin\BranchPara;
use App\Models\Admin\GroupCode;
use App\Models\Admin\ManifestDetail;
use App\Models\Admin\ManifestMaster;
use App\Models\Admin\RouteDeliveryDetail;
use App\Models\Admin\RouteDeliveryMaster;
use App\Models\Admin\RoutePara;
use App\Models\Admin\Tracking;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Collection;
use Session;
use Auth;
use App;

class ManifestController extends DashboardController
{
    public $admin_data;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        Session::forget('booking_information');
        $this->admin_data['manifest_datas'] = ManifestMaster::orderBy('id', 'desc')->get();
//        $this->admin_data['group_data'] = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->orderBy('group_code')->get();
//        $this->admin_data['group_data'] = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->orderBy('group_code')->get();
        $this->admin_data['group_data'] = BranchPara::select('g.*', 'branch_paras.*')
            ->whereNotNull('branch_paras.group_name')
            ->join('group_code as g', 'g.group_code', '=', 'branch_paras.group_code')
            ->where('g.branch_code', '=', $this->admin_data['login_user']->branch_code)->orderBy('g.group_name', 'asc')
            ->get();
        $this->admin_data['route_para'] = RoutePara::where([
            ['branch_code', '=', $this->admin_data['login_user']->branch_code],
            ['pickup_tag', '=', 'Y']
        ])->distinct()->get();
        return view('admin.manifest.index', $this->admin_data);

    }

    public function show()
    {

    }

    public function receiveBranch(Request $request)
    {
        $receive_branch = BranchPara::where('branch_code', '=', $request->group_code)->first();
        return response()->json(['success' => true, 'message' => 'Location Successfully added', 'data' => ['receive_branch' => $receive_branch->receiving_branch_code]], 200);
    }

    public function addBill(Request $request)
    {
        $data = BookingInformation::select('booking_information.*', 'c.shipper_name as shipper_name')->join('group_para', 'group_para.location_code', '=', 'booking_information.dest_location_code')
            ->leftJoin('customer_para as c', 'c.shipper_code', '=', 'booking_information.shipper_code')
            ->where('group_para.branch_code', '=', $this->admin_data['login_user']->branch_code)
            ->where('booking_information.bill_no', '=', $request->bill_no)
            ->where('group_code', '=', $request->location_code)
            ->where('manifest_no', '=', '')
            ->first();
        if (!$data) {
            $data = ManifestDetail::select('manifest_detail.*', 'c.shipper_name as shipper_name')->where('bill_no', '=', $request->bill_no)
                ->leftJoin('customer_para as c', 'c.shipper_code', '=', 'manifest_detail.shipper_code')
                ->join('group_para', 'group_para.location_code', '=', 'manifest_detail.location_to')
                ->where('group_para.branch_code', '=', $this->admin_data['login_user']->branch_code)
                ->where('group_code', '=', $request->location_code)
                ->first();
            if (!$data) {
                $data = ManifestDetail::select('manifest_detail.*', 'c.shipper_name as shipper_name')
                    ->leftJoin('customer_para as c', 'c.shipper_code', '=', 'manifest_detail.shipper_code')
                    ->where('manifest_detail.bill_no', '=', $request->bill_no)
                    ->join('route_delivery_detail as r1', 'r1.bill_no', '=', 'manifest_detail.bill_no')
                    ->where('r1.rto', '=', 'Y')
                    ->join('group_para', 'group_para.location_code', '=', 'manifest_detail.location_from')
//                    ->where('group_para.branch_code', '=', $this->admin_data['login_user']->branch_code)
                    ->where('group_para.group_code', '=', $request->location_code)
                    ->first();
            }
        }
        if ($data == null) {
            return response()->json(['success' => false, 'message' => 'Bill mismatch or no bill found', 'data' => null], 200);
        }
        $booking_information = Session::get('booking_information');
        if (!isset($booking_information))
            $booking_information = new Collection();
//        $booking_information='';
        $push = 'Y';
        $add = (object)$data->getAttributes();
        if (isset($booking_information)) {

            foreach ($booking_information as $b) {
                if ($b->bill_no == $request->bill_no) {
                    $push = 'N';
                }
            }
        }
        if ($push == 'Y') {
            $booking_information->push($add);
        }

        Session::put('booking_information', $booking_information);
        $add_bill_html = view('admin.manifest.add_bill_no', compact('booking_information'))->render();

        return response()->json(['success' => true, 'message' => 'Location Successfully added', 'data' => ['add_bill_html' => $add_bill_html]], 200);
    }

    public function pdfView(Request $request)
    {
        $this->admin_data['manifest_master'] = ManifestMaster::where('manifest_no', '=', $request->bill_no)
            ->first();
        $this->admin_data['manifest_detail'] = ManifestDetail::select('b.cod_amount','b.mailing_mode','manifest_detail.*')
            ->where('manifest_detail.manifest_no', '=', $request->bill_no)
            ->leftJoin('booking_information as b','b.bill_no','=','manifest_detail.bill_no')
            ->get();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.manifest.pdf', $this->admin_data)->render());
        $customPaper = array(0, 0, 595, 421);
        return $pdf->setPaper($customPaper)->stream();
    }

    public function deleteDetail($id)
    {

        $manifestDelete = ManifestDetail::where('bill_no', '=', $id)->delete();
        $booking_information = Session::get('booking_information');


        foreach ($booking_information as $key => $b) {
            if ($b->bill_no == $id) {
                $booking_information->forget($key);
                break;
            }
        }
        $delete_bill_html = view('admin.manifest.add_bill_no', compact('booking_information'))->render();
        return response()->json(['success' => true, 'message' => 'Bill no Successfully deleted', 'data' => ['delete_bill_html' => $delete_bill_html]], 200);
    }

    public function store(Request $request)
    {

        if ($request->manifest_no == null) {
            $branch_para = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
            $query = ManifestMaster::select(DB::raw("MAX(CAST(SUBSTRING(manifest_no, 11) AS UNSIGNED)+1) AS new_manifest_no"))->first();
            if ($query->new_manifest_no != null) {

                $manifest_no = date('Y') . $this->admin_data['login_user']->branch_code . '-00' . $query->new_manifest_no;
            } else {
                $manifest_no = date('Y') . $this->admin_data['login_user']->branch_code . '-' . '001';
            }
            $booking_information = Session::get('booking_information');
            DB::beginTransaction();
            try {
                $bill_no = '';
                ManifestMaster::create(['manifest_no' => $manifest_no, 'manifest_date' => $request->manifest_date, 'location_from' => $this->admin_data['login_user']->branch_code, 'location_to' => $request->location_to, 'remarks' => isset($request->remarks) ? $request->remarks : '', 'prepared_by' => Auth::user()->id, 'prepared_on' => date('Y-m-d'), 'received_by' => null, 'received_on' => null, 'export_tag' => '', 'branch_code' => $this->admin_data['login_user']->branch_code, 'posted_date' => null, 'receive_branch' => null, 'receive_timestamp' => null, 'type' => 'automatic_bill']);
                foreach ($booking_information as $i => $b) {

                    $input = [
                        'manifest_no', 'bill_no', 'shipper_code', 'consignee_name', 'consignee_address', 'merchandise_code', 'quantity', 'weight', 'receive_condition', 'consignee_receive', 'consignee_receive_date', 'remarks', 'branch_code',
                        'delivery_taken_by', 'delivery_taken_on', 'manifest_no_made', 'rto', 'fiscal_year', 'manifest_no_rto', 'location_from', 'location_to'
                    ];
                    foreach ($input as $j) {
                        if ($j == 'consignee_receive_date' || $j == 'delivery_taken_on') {
                            $data2[$j] = null;
                        } else {
                            $data2[$j] = isset($b->$j) ? $b->$j : '';
                        }
                    }
                    $data2['manifest_no'] = $manifest_no;
                    $data2['branch_code'] = $this->admin_data['login_user']->branch_code;
                    $data2['location_to'] = isset($b->dest_location_code) ? $b->dest_location_code : $b->location_to;
                    ManifestDetail::create($data2);
                    $branch = BranchPara::where('branch_code', '=', $data2['location_to'])->first();
                    $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
                    $attribute = [
                        'tracking_id' => $tracking_id,
                        'track_date' => date('Y-m-d'),
                        'bill_no' => $b->bill_no,
                        'reference_no' => $manifest_no,
                        'status' => 'IN TRANSIT',
                        'activity' => 'DISPATCHED',
                        'location' => $branch->branch_name,
                        'user_id' => $this->admin_data['login_user']->user_id,
                        'timestamp' => date('Y-m-d'),
                        'tag' => 'B',
                        'branch_code' => isset($b->dest_location_code) ? $b->dest_location_code : $b->location_to,
                        'crossing_no' => ''

                    ];
                    DB::table('booking_information')
                        ->where('bill_no', $b->bill_no)// find your user by their email
                        ->limit(1)// optional - to ensure only one record is updated.
                        ->update(array('manifest_no' => $manifest_no));

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

    public function receiveManifest()
    {
//        $query="SELECT * FROM MANIFEST_MASTER WHERE LOCATION_TO='".$employee_data['BRANCH_CODE']."'"." AND RECEIVE_TIMESTAMP IS NULL";
        $this->admin_data['manifests'] = ManifestMaster::where('location_to', '=', $this->admin_data['login_user']->branch_code)->where('received_by', '=', null)->get();
        Session::forget('booking_information');
        Session::forget('scan_bill_no');
        $this->admin_data['manifest_datas'] = ManifestMaster::orderBy('id', 'desc')->get();
        $this->admin_data['group_data'] = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->get();
        $this->admin_data['route_para'] = RoutePara::where([
            ['branch_code', '=', $this->admin_data['login_user']->branch_code],
            ['pickup_tag', '=', 'Y']
        ])->distinct()->get();
        return view('admin.manifest.receive.index', $this->admin_data);

    }

    public function ajaxManifestDetail(Request $request)
    {
        $manifest_details = ManifestDetail::select('manifest_detail.*', 'customer_para.shipper_name as shipper_name', 'b.cod_amount as cod_amount')
            ->leftjoin('customer_para', 'customer_para.shipper_code', '=', 'manifest_detail.shipper_code')
            ->join('booking_information as b', 'b.bill_no', '=', 'manifest_detail.bill_no')
            ->where('manifest_detail.manifest_no', '=', $request->manifest_no)
            ->get();
        $manifest_master = ManifestMaster::select('manifest_master.*', 'l.branch_name as location_name_from', 'l2.branch_name as location_name_to')->where('manifest_no', '=', $request->manifest_no)->leftjoin('branch_paras as l', 'l.branch_code', '=', 'manifest_master.location_from')->leftjoin('branch_paras as l2', 'l2.branch_code', '=', 'manifest_master.location_to')->first();
//        if ($data == null) {
//            return response()->json(['success' => false, 'message' => 'Bill mismatch or no bill found', 'data' => null], 200);
//        }
//        $booking_information = Session::get('booking_information');
//        if (!isset($booking_information))
//            $booking_information = new Collection();
//        $push = 'Y';
//        $add = (object)$data->getAttributes();
//        if (isset($booking_information)) {
//
//            foreach ($booking_information as $b) {
//                if ($b->bill_no == $request->bill_no) {
//                    $push = 'N';
//                }
//            }
//        }
//        if ($push == 'Y') {
//            $booking_information->push($add);
//        }

//        Session::put('booking_information', $booking_information);
        $manifest_detail_html = view('admin.manifest.receive.ajax_manifest_detail', compact('manifest_details'))->render();

        return response()->json(['success' => true, 'message' => 'Manifest Details', 'data' => ['manifest_detail_html' => $manifest_detail_html, 'manifest_master' => $manifest_master]], 200);
    }

    public function receiveManifestStore(Request $request)
    {
        ManifestMaster::where('manifest_no', '=', $request->manifest_no)
            ->update(['received_by' => $this->admin_data['login_user']->id, 'received_on' => date('Y-m-d')]);
//        $manifest_details = ManifestDetail::select('manifest_detail.*')
//            ->where('manifest_detail.manifest_no', '=', $request->manifest_no)
//            ->get();
//        foreach ($request->bill_no as $key => $b) {
//
//            ManifestDetail::where([
//                ['manifest_no', '=', $request->manifest_no],
//                ['bill_no', '=', $b]
//            ])->update(['receive_condition' => $request->status[$key]]);
//            $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
//            $attribute = [
//                'tracking_id' => $tracking_id,
//                'track_date' => date('Y-m-d'),
//                'bill_no' => $b,
//                'reference_no' => $request->manifest_no,
//                'status' => 'ARRIVED HUB',
//                'activity' => 'ARRIVED HUB',
//                'location' => '',
//                'user_id' => $this->admin_data['login_user']->user_id,
//                'timestamp' => date('Y-m-d'),
//                'tag' => 'MR',
//                'branch_code' => $this->admin_data['login_user']->branch_code,
//                'crossing_no' => ''
//
//            ];
//            Tracking::create($attribute);
//
//        }
        return response()->json(['success' => true, 'message' => 'Manifest received successfully', 'data' => null], 200);
    }

    public function consigneeReceiveIndex()
    {

//        $query = "SELECT DISTINCT RTDD.DELIVERY_NO, DELIVERY_DATE FROM RTDM, RTDD
//			WHERE RTDM.DELIVERY_NO = RTDD.DELIVERY_NO
//				AND RECEIVED_BY IS NULL
//				AND RTDM.BRANCH_CODE = '" . $_SESSION['branchCode'] . "'
//			ORDER BY DELIVERY_DATE DESC";
//        $stmtRTDD = oci_parse($c, $query);
//        oci_execute($stmtRTDD);
        $this->admin_data['cr_list'] = RouteDeliveryDetail::where('received_by', '!=', '')->orderBy('id', 'desc')->get();
        $this->admin_data['stmtRTDD'] = RouteDeliveryMaster::select('delivery_no', 'delivery_date', 'master_id')
            ->join('route_delivery_detail as r', 'r.master_id', '=', 'route_delivery_master.id')->distinct('delivery_no')
            ->where('r.received_by', '=', '')
            ->where('r.branch_code', '=', $this->admin_data['login_user']->branch_code)
            ->get();
        Session::forget('booking_information');
        return view('admin.manifest.consignee_receive.index', $this->admin_data);
    }

    public function consigneeReceiveDetail(Request $request)
    {
        $delivery_details = RouteDeliveryDetail::where('master_id', '=', $request->delivery_no)
            ->where('received_by', '=', '')->get();
        $delivery_detail_html = view('admin.manifest.consignee_receive.detail', compact('delivery_details'))->render();
        return response()->json(['success' => true, 'message' => 'Manifest Details', 'data' => ['manifest_detail_html' => $delivery_detail_html]], 200);

    }

    public function consigneeReceiveStore(Request $request)
    {
        foreach ($request->remarks as $key => $r) {
            if ($request->received[$key] != null) {
                $received_by = $request->received[$key];
            } else {
                $received_by = $request->received_by[$key];
            }
            $route_data = RouteDeliveryDetail::where([
                ['bill_no', '=', $request->bill_no[$key]],
                ['master_id', '=', $request->delivery_no]
            ])->update(['received_by' => isset($received_by) ? $received_by : '', 'received_on' => $request->received_on[$key], 'remarks' => isset($request->remarks[$key]) ? $request->remarks[$key] : '', 'rto' => isset($request->rto[$key]) ? 'Y' : '']);
            ManifestDetail::where('bill_no', '=', $request->bill_no[$key])->update(['consignee_receive' => 'Y', 'consignee_receive_date' => $request->received_on[$key]]);
            $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
            $attribute = [
                'tracking_id' => $tracking_id,
                'track_date' => date('Y-m-d'),
                'bill_no' => $request->bill_no[$key],
                'reference_no' => $request->delivery_no,
                'status' => 'DELIVERED',
                'activity' => 'RECEIVED BY ' . $received_by,
                'location' => '',
                'user_id' => $this->admin_data['login_user']->user_id,
                'timestamp' => date('Y-m-d'),
                'tag' => 'RD',
                'branch_code' => $this->admin_data['login_user']->branch_code,
                'crossing_no' => ''

            ];

            Tracking::create($attribute);
        }
        return response()->json(['success' => true, 'message' => 'COnsignee received updated successfully', 'data' => null], 200);


    }

    public function destroy($id)
    {
        $manifest = ManifestMaster::find($id);
        ManifestDetail::where('manifest_no', '=', $manifest->manifest_no)->delete();
        ManifestMaster::where('id', '=', $id)->delete();

        Session::flash('successMsg', 'Manifest Parameter has been deleted successfully');
        return response()->json(['success' => true, 'message' => 'Manifest Parameter has been deleted successfully'], 200);
    }

    public function editManifest($id)
    {
        $this->admin_data['manifest'] = ManifestMaster::find($id);
        $this->admin_data['manifest_details'] = ManifestDetail::select('booking_information.*', 'manifest_detail.*')
            ->where('manifest_detail.manifest_no', '=', $this->admin_data['manifest']->manifest_no)
            ->join('booking_information', 'booking_information.bill_no', '=', 'manifest_detail.bill_no')
            ->get();

//        $group_data = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->get();
        return view('admin.manifest.edit', $this->admin_data);
    }

    public function delete_bill_data($id)
    {
        $manifest_data = ManifestDetail::find($id);
        $manifest_data->delete();
        Session::flash('successMsg', 'Bill data has been deleted successfully');
        return response()->json(['success' => true, 'message' => 'Bill data has been deleted successfully'], 200);
    }

    public function update_manifest(Request $request, $id)
    {
        $manifest = ManifestMaster::find($id);
        $manifest->manifest_date = $request->manifest_date;
        $manifest->remarks = $request->remarks;
        $manifest->save();
        Session::flash('successMsg', 'Updated successfully');
        return redirect()->back();

    }

    public function addScanBill(Request $request)
    {
        $ses_bill_no = Session::get('scan_bill_no');
        if (!isset($ses_bill_no))
            $ses_bill_no = new Collection();
        $push = 'Y';

        $add = (object)['scan_bill_no' => $request->scan_bill_no];
        if (isset($ses_bill_no)) {

            foreach ($ses_bill_no as $b) {
                if ($b->scan_bill_no == $request->scan_bill_no) {
                    $push = 'N';
                }
            }
        }
        if ($push == 'Y') {
            $ses_bill_no->push($add);
        }
        Session::put('scan_bill_no', $ses_bill_no);


        $add_scan_bill_html = view('admin.manifest.receive.add_bill_no', compact('ses_bill_no'))->render();
        return response()->json(['success' => true, 'message' => 'Bill Successfully added', 'data' => ['add_scan_bill_html' => $add_scan_bill_html]], 200);
    }

    public function scanBillDelete($id)
    {
        $ses_bill_no = Session::get('scan_bill_no');
        foreach ($ses_bill_no as $key => $b) {
            if ($b->scan_bill_no == $id) {
                $ses_bill_no->forget($key);
                break;
            }
        }
        $add_scan_bill_html = view('admin.manifest.receive.add_bill_no', compact('ses_bill_no'))->render();
        return response()->json(['success' => true, 'message' => 'Bill no Successfully deleted', 'data' => ['add_scan_bill_html' => $add_scan_bill_html]], 200);
    }

    public function compareScanBill(Request $request)
    {
        //bill add
        $ses_bill_no = Session::get('scan_bill_no');
        if (!isset($ses_bill_no))
            $ses_bill_no = new Collection();
        $push = 'Y';

        $add = (object)['scan_bill_no' => $request->scan_bill_no];
        if (isset($ses_bill_no)) {

            foreach ($ses_bill_no as $b) {
                if ($b->scan_bill_no == $request->scan_bill_no) {
                    $push = 'N';
                }
            }
        }
        if ($push == 'Y') {
            $ses_bill_no->push($add);
        }
        //finish bill add
        Session::put('scan_bill_no', $ses_bill_no);
        //bill save
        ManifestDetail::where([
            ['manifest_no', '=', $request->manifest_no],
            ['bill_no', '=', $request->scan_bill_no]
        ])->update(['receive_condition' => 'R']);
        $tracking_id = str_pad(DB::table('tracking')->max('tracking_id') + 1, 20, 0, STR_PAD_LEFT);
        $attribute = [
            'tracking_id' => $tracking_id,
            'track_date' => date('Y-m-d'),
            'bill_no' => $request->scan_bill_no,
            'reference_no' => $request->manifest_no,
            'status' => 'ARRIVED HUB',
            'activity' => 'ARRIVED HUB',
            'location' => '',
            'user_id' => $this->admin_data['login_user']->user_id,
            'timestamp' => date('Y-m-d'),
            'tag' => 'MR',
            'branch_code' => $this->admin_data['login_user']->branch_code,
            'crossing_no' => ''
        ];
        $check_track = Tracking::where('bill_no', '=', $request->scan_bill_no)->where('status', '=', 'ARRIVED HUB')->first();
        if (!$check_track) {
            Tracking::create($attribute);
        }
        //finish bill save

        $manifest_details = ManifestDetail::select('manifest_detail.*', 'customer_para.customer_name as customer_name')
            ->leftjoin('customer_para', 'customer_para.shipper_code', '=', 'manifest_detail.shipper_code')
            ->where('manifest_detail.manifest_no', '=', $request->manifest_no)
            ->get();
        $manifest_master = ManifestMaster::where('manifest_no', '=', $request->manifest_no)->first();
        $compare_scan_bill_html = view('admin.manifest.receive.compare_bill', compact('manifest_details', 'ses_bill_no', 'manifest_master'))->render();
        $compare_manifest_details_html = view('admin.manifest.receive.compare_manifest_details', compact('manifest_details', 'ses_bill_no', 'manifest_master'))->render();

        return response()->json(['success' => true, 'message' => 'Bill no Successfully deleted', 'data' => ['compare_scan_bill_html' => $compare_scan_bill_html, 'compare_manifest_details_html' => $compare_manifest_details_html]], 200);

    }

    public function apiManifestHomepage(Request $request)
    {
        $manifest_data = ManifestMaster::where('location_to', '=', $this->admin_data['login_user']->branch_code)->where('received_by', '=', null)->get();
        return response()->json([
            'success' => true,
            'message' => 'Manifest Data',
            'data' => [
                'manifest_data' => $manifest_data]
        ], 200);

    }

    public function apiManifestDetail($manifest_no)
    {
        $manifest_detail = ManifestDetail::where('manifest_no', '=', $manifest_no)->get();
        return response()->json([
            'success' => true,
            'message' => 'Manifest Data',
            'data' => [
                'manifest_detail' => $manifest_detail]
        ], 200);

    }


}
