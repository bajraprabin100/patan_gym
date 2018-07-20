<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\AgentPara;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BranchPara;
use DB;
use Session;

class AgentParaController extends DashboardController
{
    public function index()
    {
        $this->admin_data['branch_paras'] = BranchPara::all();
        $this->admin_data['agent_paras'] = AgentPara::orderBy('id', 'desc')->get();
        return view('admin.agent_para.index', $this->admin_data);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $input = [
            'agent_id', 'agent_name', 'address', 'telephone', 'fax_no', 'email', 'ceo_md', 'ceo_mobileno', 'contract_person', 'mobile_no', 'type', 'remarks', 'branch_code'
        ];
        foreach ($input as $i) {
            $data[$i] = isset($data[$i]) ? $data[$i] : '';
        }
        $query = AgentPara::select(DB::raw("MAX(CAST(SUBSTRING(agent_id, 5) AS UNSIGNED)+1) AS Agent_id"))->first();
        if ($query->Agent_id != null) {
            $data['agent_id'] = 'A' . $request->branch_code . $query->Customer_code;
        } else {
            $data['agent_id'] = 'A' . $request->branch_code . '1';
        }
        AgentPara::create($data);
//        $request->session()->flash('successMsg', 'success');
        Session::flash('successMsg', 'Agent Parameter saved successfully');
        return response()->json(['success' => true, 'message' => 'Agent Parameter saved successfully'], 200);
    }

    public function edit($id)
    {
        $branch_para = BranchPara::all();
        $agent_info = AgentPara::find($id);
        $agent_info_html = view('admin.agent_para.ajaxEdit', compact('agent_info','branch_para'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['agent_info_html' => $agent_info_html]], 200);

    }

    public function update_agent(Request $request, $id)
    {
        $agent=AgentPara::find($id);
        $agent->update($request->all());
        Session::flash('successMsg','Agent Parameter updated successfully');
        return response()->json(['success'=>true,'message'=>'Agent Parameter updated successfully'],200);

    }
    public function destroy_agent($id){
        $agent = AgentPara::find($id);
        $agent->delete();
        Session::flash('successMsg','Agent Parameter deleted successfully');
        return response()->json(['success'=>true,'message'=>'Agent Parameter deleted successfully'],200);
    }

}
