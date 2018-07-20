<?php
/**
 * Created by PhpStorm.
 * User: view9
 * Date: 5/27/18
 * Time: 10:56 AM
 */

namespace App\Http\Controllers\Admin;


use App\Models\Admin\BookingInformation;
use App\Models\Admin\CreditStatementDetail;
use App\Models\Admin\CreditStatementMaster;
use App\Models\Admin\CustomerPara;
use App\Models\Admin\ManifestMaster;
use Illuminate\Http\Request;
use Session;
use DB;

class CreditStatementController extends DashboardController
{
    protected $admin_data;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        Session::forget('branch_delivery_information');
        $this->admin_data['statement_masters'] = CreditStatementMaster::orderBy('id', 'desc')->get();
        $this->admin_data['customers'] = CustomerPara::where("branch_code", '=', $this->admin_data['login_user']->branch_code)->get();
        return view('admin.credit_statement.index', $this->admin_data);
    }

    public function getStatement(Request $request)
    {
        $c_statement = BookingInformation::where([
            ['shipper_code', '=', $request->shipper_code],
            ['payment_mode', '=', 'Credit'],
            ['statement_no', '=', '']

        ])
            ->whereBetween('book_date', [$request->date_from_ad, $request->date_to_ad])
            ->where('branch_code', '=', $this->admin_data['login_user']->branch_code)
            ->orderBy('book_date', 'desc')
            ->get();
        Session::put('ses_statement', $c_statement);
        $c_s_html = view('admin.credit_statement.ajax_credit_statement', compact('c_statement'))->render();

//        return response()->json(['success' => true, 'message' => 'Manifest Details', 'data' => ['manifest_detail_html' => $manifest_detail_html, 'manifest_master' => $manifest_master]], 200);
        return response()->json(['success' => true, 'message' => 'Bill no Successfully added', 'data' => ['add_bill_html' => $c_s_html]], 200);
    }


    public function store(Request $request)
    {
        if (!isset($request->statement_no)) {
            $query = CreditStatementMaster::select(DB::raw("MAX(CAST(SUBSTRING(statement_no, 11) AS UNSIGNED)+1) AS new_statement_no"))->first();
            if ($query->new_statement_no != null) {

                $statementNo = date('Y') . $this->admin_data['login_user']->branch_code . '-00' . $query->new_statement_no;
            } else {
                $statementNo = date('Y') . $this->admin_data['login_user']->branch_code . '-' . '001';
            }
            $ses_statement = Session::get('ses_statement');
            DB::beginTransaction();
            try {
                $c_s = CreditStatementMaster::create(['statement_no' => $statementNo, 'statement_date' => $request->statement_date_ad, 'date_from' => $request->date_from_ad, 'date_to' => $request->date_to_ad, 'shipper_code' => $request->shipper_code, 'prepared_by' => $this->admin_data['login_user']->employee_name, 'prepared_on' => date('Y-m-d'), 'branch_code' => $this->admin_data['login_user']->branch_code]);
                foreach ($request->bill_no as $i => $b) {
                    CreditStatementDetail::create(['statement_master_id' => $c_s->id, 'statement_no' => $statementNo, 'bill_date' => $request->bill_date[$i], 'bill_no' => $request->bill_no[$i], 'remarks' => isset($request->remarks[$i]) ? $request->remarks[$i] : '']);
                    BookingInformation::where('bill_no', '=', $request->bill_no[$i])
                        ->update([
                            'mailing_mode' => $request->mailing_mode[$i],
                            'quantity' => $request->quantity[$i],
                            'weight' => $request->weight[$i],
                            'weight_charge' => $request->weight_charge[$i],
                            'other_charge' => $request->other_charge[$i],
                        ]);
                  
                }
                DB::table('booking_information')
                    ->where('bill_no', $b)  // find your user by their email
                    ->limit(1)  // optional - to ensure only one record is updated.
                    ->update(array('statement_no' => $statementNo));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Session::flash('successMsg', $e->getMessage());
                dd($e->getMessage());
                // something went wrong
            }

            Session::flash('successMsg', 'Saved Successfully');
            return response()->json(['success' => true, 'message' => 'Saved successfully', 'data' => ['statement_no' => $statementNo]], 200);
        }

    }

    public function deleteBill(Request $request)
    {
        $c_statement = Session::get('ses_statement');
        foreach ($c_statement as $key => $g) {
            if ($g->bill_no == $request->bill_no) {
                $c_statement->forget($key);
                break;
            }
        }
        $c_statement_html = view('admin.credit_statement.ajax_credit_statement', compact('c_statement'))->render();
        return response()->json(['success' => true, 'message' => 'Bill no Successfully deleted', 'data' => ['add_bill_html' => $c_statement_html]], 200);

    }

    public function edit_statement($id)
    {
        $this->admin_data['credit_statement'] = CreditStatementMaster::find($id)->first();
        $this->admin_data['credit_details'] = CreditStatementDetail::select('credit_statement_detail.*', 'booking_information.*')
            ->where('statement_master_id', '=', $id)
            ->join('booking_information', 'booking_information.bill_no', '=', 'credit_statement_detail.bill_no')
            ->get();
        $this->admin_data['customers'] = CustomerPara::where("branch_code", '=', $this->admin_data['login_user']->branch_code)->get();

        return view('admin.credit_statement.edit_credit_statement', $this->admin_data);
    }

    public function update_statement(Request $request, $id)
    {
//        dd($request->all());
        try {
//            $c_s = CreditStatementMaster::where(update(['statement_date' => $request->statement_date_ad, 'date_from' => $request->date_from_ad, 'date_to' => $request->date_to_ad, 'shipper_code' => $request->shipper_code, 'prepared_by' => $this->admin_data['login_user']->employee_name, 'prepared_on' => date('Y-m-d'), 'branch_code' => $this->admin_data['login_user']->branch_code]);
            foreach ($request->bill_no as $i => $b) {


                CreditStatementDetail::where('bill_no', '=', $request->bill_no[$i])
                    ->update(['remarks' => isset($request->remarks[$i]) ? $request->remarks[$i] : '']);
                BookingInformation::where('bill_no', '=', $request->bill_no[$i])
                    ->update([
                        'mailing_mode' => $request->mailing_mode[$i],
                        'quantity' => $request->quantity[$i],
                        'weight' => $request->weight[$i],
                        'weight_charge' => $request->weight_charge[$i],
                        'other_charge' => $request->other_charge[$i],
                    ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('successMsg', $e->getMessage());
            dd($e->getMessage());
            // something went wrong
        }

        Session::flash('successMsg', 'Updated Successfully');
        return response()->json(['success' => true, 'message' => 'Updated successfully', 'data' => null], 200);


    }

    public function deleteStatementBill($id)
    {
        $statement_bill = CreditStatementDetail::find($id);
        CreditStatementDetail::where('bill_no', '=', $id)->delete();

        Session::flash('successMsg', 'Bill number has been deleted Successfully');
        return response()->json(['success' => true, 'message' => 'Bill no Successfully deleted', 'data' => null], 200);

    }

    public function deleteCreditStatement($id)
    {
        $credit_statement = CreditStatementMaster::find($id);
        CreditStatementMaster::where('id','=',$id)->delete();
        CreditStatementDetail::where('statement_master_id','=',$id)->delete();

        Session::flash('successMsg', 'Deleted Successfully');
        return response()->json(['success' => true, 'message' => 'Deleted Successfully', 'data' => null], 200);

    }

}