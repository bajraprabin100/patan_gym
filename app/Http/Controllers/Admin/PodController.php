<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\PodRecordMaster;
use App\User;
use Illuminate\Http\Request;
use App\Models\Admin\PodRecordDetail;
use Illuminate\Support\Collection;
use Session;
use DB;

class PodController extends DashboardController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->admin_data['pod_masters'] = PodRecordMaster::all();
        Session::forget('ses_pod');
        return view('admin.pod.index', $this->admin_data);
    }

    public function addBill(Request $request)
    {
        $ses_pod = Session::get('ses_pod');
        if (!isset($ses_pod))
            $ses_pod = new Collection();
        $push = 'Y';

        $add = (object)['bill_no' => $request->bill_no];
        if (isset($ses_pod)) {

            foreach ($ses_pod as $b) {
                if ($b->bill_no == $request->bill_no) {
                    $push = 'N';
                }
            }
        }
        if ($push == 'Y') {
            $ses_pod->push($add);
        }
//        $ses_pod->push($add);
        Session::put('ses_pod', $ses_pod);
        $add_bill_html = view('admin.pod.add_bill_no', compact('ses_pod'))->render();
        return response()->json(['success' => true, 'message' => 'Bill Successfully added', 'data' => ['add_bill_html' => $add_bill_html]], 200);
    }

    public function delete(Request $request, $id)
    {
        $ses_pod = Session::get('ses_pod');
        foreach ($ses_pod as $key => $b) {
            if ($b->bill_no == $id) {
                $ses_pod->forget($key);
                break;
            }
        }
        $add_bill_html = view('admin.pod.add_bill_no', compact('ses_pod'))->render();
        return response()->json(['success' => true, 'message' => 'Bill no Successfully deleted', 'data' => ['add_bill_html' => $add_bill_html]], 200);
    }

    public function store(Request $request)
    {
        $query = PodRecordMaster::select(DB::raw("MAX(CAST(SUBSTRING(record_no, 11) AS UNSIGNED)+1) AS new_record_no"))->first();
        if ($query->new_record_no != null) {

            $record_no = date('Y') . $this->admin_data['login_user']->branch_code . '-00' . $query->new_record_no;
        } else {
            $record_no = date('Y') . $this->admin_data['login_user']->branch_code . '-' . '001';
        }
        $ses_pod = Session::get('ses_pod');
        DB::beginTransaction();
        try {
            $attribute = [
                'record_no' => $record_no,
                'record_date' => $request->record_date,
                'prepared_by' => $request->prepared_by,
                'timestamp' => date('Y-m-d'),
                'branch_code' => $request->branch_code
            ];
            $pod_master = PodRecordMaster::create($attribute);

            foreach ($ses_pod as $v) {
                $data = [
                    'pod_master_id' => $pod_master->id,
                    'record_no' => $record_no,
                    'bill_no' => $v->bill_no
                ];

                PodRecordDetail::create($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('successMsg', $e->getMessage());
            dd($e->getMessage());
            // something went wrong
        }
        Session::flash('successMsg', 'POD Record ' . $record_no . ' added successfully');
        return response()->json(['success' => true, 'message' => 'POD Record added successfully', 'data' => null], 200);


    }

    public function editPodMaster($id)
    {
        $this->admin_data['pod_details'] = PodRecordMaster::select('pod_record_masters.*', 'pod_record_details.*')
            ->where('pod_record_masters.id', '=', $id)
            ->join('pod_record_details', 'pod_record_details.record_no', '=', 'pod_record_masters.record_no')
            ->get();
        return view('admin.pod.edit_pod_record', $this->admin_data);
    }

    public function delete_pod_master($id)
    {
        $delete_pod_master = PodRecordMaster::find($id);
        $delete_pod_master->delete();
        Session::flash('successMsg', 'POD Record deleted Successfully');
        return response()->json(['success' => true, 'message' => 'POD Record Deleted Successfully', 'data' => null], 200);

    }
    public function delete_master_bill($id){
        $delete_master_bill = PodRecordDetail::find($id);
        $delete_master_bill->delete();
        Session::flash ('successMsg','Bill number has been Deleted Successfully');
        return response()->json(['success'=>true,'message','Bill number has been Deleted Successfully','data'=>null],200);
    }
}
