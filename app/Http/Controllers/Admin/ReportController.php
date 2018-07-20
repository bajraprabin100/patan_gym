<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BookingInformation;
use App\Models\Admin\BranchPara;
use App\Models\Admin\CreditStatementMaster;
use App\Models\Admin\LocationHierarachy;
use App\Models\Admin\CustomerPara;
use App\Models\Admin\ManifestDetail;
use App\Models\Admin\ManifestMaster;
use App\Repositories\Backend\Report\ReportInterface;
use App\Models\Admin\RouteDeliveryMaster;
use App\Models\Admin\ShippingPara;
use Illuminate\Support\Facades\App;
use PDF;
use Symfony\Component\HttpFoundation\Request;
use DB;

use App\Models\Admin\Tracking;


class ReportController extends DashboardController
{

    public $report;

    public function __construct(ReportInterface $report)
    {

        parent::__construct();
        $this->report = $report;
    }

    public function trackingIndex()
    {
        return view('admin.report.tracking.index', $this->admin_data);
    }


    public function trackBillNo(Request $request)
    {
//        $query = "SELECT GET_GROUP_NAME(ORG_LOCATION_CODE, BRANCH_CODE) ORIGIN, GET_LOCATION_NAME(DEST_LOCATION_CODE) DESTINATION,
//								BOOK_DATE
//							FROM BKIN WHERE BILL_NO = '".$dataTRK->BILL_NO."'";
        $trk_data = Tracking::select('tracking.*',DB::raw("DATE_FORMAT(created_at,'%H:%i:%s') time"))->where([
            ['bill_no', '=', $request->bill_no],
            ['crossing_no', '=', isset($request->crossing_no) ? $request->crossing_no : '']
        ])
            ->orderBy('id', 'asc')
            ->get();

        $b_i = BookingInformation::selectRaw('GET_GROUP_NAME(org_location_code, branch_code) as origin,GET_LOCATION_NAME(dest_location_code) destination,bill_no')
            ->where('bill_no', '=', $request->bill_no)->first();
        if(!$b_i){
            $b_i = ManifestDetail::selectRaw('GET_GROUP_NAME(location_from, branch_code) as origin,GET_LOCATION_NAME(location_to) destination,bill_no')
                ->where('bill_no', '=', $request->bill_no)->first();

        }
        if(isset($request->type) && $request->type=='tracking'){
            $html=view('admin.report.tracking.pdf', compact('trk_data', 'b_i'))->render();
            return response()->json(['success'=>true,'message'=>'','data'=>['html'=>$html]],200);

        }
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.tracking.pdf', compact('trk_data', 'b_i'))->render());
        return $pdf->stream();
    }

    public function apiTrackBillNo(Request $request)
    {
        $trk_data = Tracking::where([
            ['bill_no', '=', $request->billNo],
            ['crossing_no', '=', isset($request->crossing_no) ? $request->crossing_no : '']
        ])
            ->orderBy('id', 'asc')
            ->get();

        $b_i = BookingInformation::selectRaw('GET_GROUP_NAME(org_location_code, branch_code) as origin,book_date,GET_LOCATION_NAME(dest_location_code) destination,bill_no')
            ->where('bill_no', '=', $request->billNo)->first();
        foreach ($trk_data as $t) {

            $status = $t->status;
        }
        return response()->json([
            'success' => true,
            'message' => 'Tracking date received',
            'data' => [
                'tracking_data' => $trk_data,
                'booking_data' => $b_i,
                'status' => $status
            ]
        ], 200);
    }

    public function bookingIndex()
    {
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->get();
        $this->admin_data['customer'] = CustomerPara::orderBy('id', 'desc')->get();
        return view('admin.report.bookingDetail.index', $this->admin_data);

    }
    public function bookingPdf(Request $request)
    {
        $this->admin_data['booking'] = $request->all();

        if ($request->reportType == 'L') {
            $group_code = BranchPara::select('group_code')->where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
            $this->admin_data['query'] = $this->report->bookingList($request->all(), $this->admin_data['login_user'],$group_code);
        } else {
            $this->admin_data['query'] = BookingInformation::join('customer_para as c', 'c.shipper_code', '=', 'booking_information.shipper_code')
                ->whereBetween('booking_information.book_date', [$request->fromDate, $request->toDate])
                ->where('booking_information.shipper_code', '=', $request->shipper_code)
                ->where('booking_information.dest_location_code', '=', $request->location_code)
                ->where('booking_information.payment_mode', '=', $request->payment_mode)
                ->where('booking_information.crossing_no', '=', isset($request->crossing_no) ? $request->crossing_no : '')
                ->get();

        }
        if(isset($request->type) && $request->type=='booking'){
            $html=view('admin.report.bookingDetail.groupPdf', $this->admin_data)->render();
            return response()->json(['success'=>true,'message'=>'','data'=>['html'=>$html]],200);

        }
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.bookingDetail.groupPdf', $this->admin_data)->render());
        return $pdf->setPaper('A2', 'portrait')->stream();
    }

    public function dailySalesIndex()
    {
        return view('admin.report.dailySales.index', $this->admin_data);
    }

    public function dailysalesReport(Request $request)
    {
        $this->admin_data['salesReport'] = $request->all();
        $this->admin_data['branch_code'] = BranchPara::select('branch_code', 'branch_name')->where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();

        $this->admin_data['query'] = BookingInformation::selectRaw('booking_information.*,c.*,GET_LOCATION_NAME(dest_location_code) as location_name')->join('branch_paras as b', 'b.branch_code', '=', 'booking_information.branch_code')
            ->join('customer_para as c', 'c.shipper_code', '=', 'booking_information.shipper_code')
            ->whereBetween('booking_information.book_date', [$request->fromDate, $request->toDate])
            ->where('booking_information.payment_mode', '=', $request->payment_mode)
            ->where('booking_information.branch_code', '=', $this->admin_data['branch_code']->branch_code)
            ->orderBy(DB::raw('LENGTH(booking_information.bill_no), booking_information.bill_no'))
            ->get();
        if(isset($request->type) && $request->type=='dailySales'){
            $html=view('admin.report.dailySales.pdf', $this->admin_data)->render();
            return response()->json(['success'=>true,'message'=>'Daily sales report','data'=>['html'=>$html]],200);
        }
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.dailySales.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A3', 'portrait')->stream();


    }

    public function misReportIndex()
    {
        return view('admin.report.mis.index', $this->admin_data);
    }

    public function misReport(Request $request)
    {
        $this->admin_data['misReport'] = $request->all();
        $this->admin_data['query'] = BookingInformation::select('manifest_master.*', 'route_delivery_master.*', 'route_delivery_detail.*', 'booking_information.*')
            ->where(function ($query) use ($request) {
                if (isset($request->bill_no))
                    $query->where('booking_information.bill_no', '=', $request->bill_no);
            })
            ->leftjoin('manifest_master', 'manifest_master.manifest_no', '=', 'booking_information.manifest_no')
            ->leftjoin('route_delivery_master', 'route_delivery_master.delivery_no', '=', 'booking_information.delivery_no')
            ->leftjoin('route_delivery_detail', 'route_delivery_detail.master_id', '=', 'route_delivery_master.id')
            ->whereBetween('booking_information.book_date', [$request->fromDate, $request->toDate])
            ->get();
        if(isset($request->type) && $request->type=='mis'){
            $html=view('admin.report.mis.pdf', $this->admin_data)->render();
            return response()->json(['success'=>true,'message'=>'Daily sales report','data'=>['html'=>$html]],200);
        }
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.mis.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A3', 'portrait')->stream();
    }

    public function manifest_detail(Request $request)
    {
        $this->admin_data['branch_paras'] = BranchPara::orderBy('branch_name', 'asc')->get();
        $this->admin_data['customers'] = CustomerPara::orderBy('customer_name', 'asc')->get();
        return view('admin.report.manifest.index', $this->admin_data);

    }

    public function manifest_search(Request $request)
    {
        $this->admin_data['manifest_report'] = $this->report->manifestQuery($request->all(), $this->admin_data['login_user']);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.manifest.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A2', 'portrait')->stream();
    }

    public function creditStatementBillsIndex()
    {
        $this->admin_data['statements'] = CreditStatementMaster::where('branch_code', '=', $this->admin_data['login_user']->branch_code)
            ->orderby('statement_no', 'asc')->get();
        $this->admin_data['customers'] = CustomerPara::orderBy('customer_name', 'asc')->get();
        return view('admin.report.credit_statement_bills.index', $this->admin_data);
    }

    public function creditStatementBillsPdf(Request $request)
    {

        if ($request->type == 'Report') {
            $this->admin_data['credit_statement_bills'] = $this->report->creditStatementBillQuery($request->all(), $this->admin_data['login_user']);
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.report.credit_statement_bills.type_report', $this->admin_data)->render());
            return $pdf->setPaper('A3', 'portrait')->stream();

        }
    }

    public function creditStatementListIndex(Request $request)
    {
        $this->admin_data['statements'] = CreditStatementMaster::where('branch_code', '=', $this->admin_data['login_user']->branch_code)
            ->orderby('statement_no', 'asc')->get();
        $this->admin_data['customers'] = CustomerPara::orderBy('customer_name', 'asc')->get();
        return view('admin.report.credit_statement_list.index', $this->admin_data);
    }

    public function creditStatementListPdf(Request $request)
    {
        $this->admin_data['credit_statement_list'] = $this->report->creditStatementListQuery($request->all(), $this->admin_data['login_user']);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.credit_statement_list.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A3', 'portrait')->stream();
    }

    public function statementwiseIndex(Request $request)
    {
        return view('admin.report.statement_wise.index', $this->admin_data);
    }

    public function statementwisePdf(Request $request)
    {
        $this->admin_data['attributes'] = $request->all();
        $this->admin_data['sw_report'] = $this->report->statementwiseQuery($request->all(), $this->admin_data['login_user']);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.statement_wise.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A3', 'portrait')->stream();
    }

    public function statementDeliveryIndex(Request $request)
    {
        return view('admin.report.statement_delivery.index', $this->admin_data);
    }

    public function statementDeliveryPdf(Request $request)
    {
        $this->admin_data['attributes'] = $request->all();
        $this->admin_data['sw_report'] = $this->report->statementDeliveryQuery($request->all(), $this->admin_data['login_user']);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.statement_delivery.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A3', 'portrait')->stream();
    }

    public function internationalBookingIndex(Request $request)
    {
        return view('admin.report.international_booking.index', $this->admin_data);

    }

    public function internationalBookingPdf(Request $request)
    {
        $this->admin_data['attributes'] = $request->all();
        $this->admin_data['ib_report'] = $this->report->internationalBookingQuery($request->all(), $this->admin_data['login_user']);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.international_booking.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A4', 'portrait')->stream();

    }

    public function documentDeliveryIndex()
    {
        return view('admin.report.documentDelivery.index', $this->admin_data);
    }

    public function documentDeliveryReport(Request $request)
    {
        $this->admin_data['report'] = $request->all();
        $this->admin_data['bill_no'] = BookingInformation::select('bill_no')->where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
        $this->admin_data['dd_query'] = $this->report->documentDeliveryQuery($request->all(), $this->admin_data['login_user']);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.documentDelivery.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A4', 'portrait')->stream();
    }

    public function podIndex()
    {
        return view('admin.report.pod.index', $this->admin_data);
    }

    public function podReport(Request $request)
    {

        $this->admin_data['query'] = $this->report->podReport($request->all(), $this->admin_data['login_user']);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.pod.podPdf', $this->admin_data)->render());
        return $pdf->setPaper('A4', 'portrait')->stream();
    }


    public function cnReport(Request $request)
    {
        $this->admin_data['query'] = $this->report->podReport($request->all(), $this->admin_data['login_user']);

//        dd( $this->admin_data['query'] );
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.report.pod.cnPdf', $this->admin_data)->render());
        $customPaper = array(0,0,595,421);
        return $pdf->setPaper($customPaper)->stream();
    }
}
