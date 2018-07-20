<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BranchPara;
use App\Models\Admin\GroupCode;
use App\Models\Admin\LocationHierarachy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Admin\GroupPara;
use DB;
use Input;
use Excel;

class GroupController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->admin_data['branch_para'] = BranchPara::whereNotNull('group_name')->orderBy('group_name')->get();
        $this->admin_data['groupCode'] = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->orderBy('group_code')->get();
        $sn = 0;
 //
//        $countGP = count($arrayGP);
        return view('admin.group.index', $this->admin_data);
    }

    public function store(Request $request)
    {
        for ($a = 0; $a < $request->rows; $a++) {
            $groupCode = $request['groupCode_' . $a];
            $groupName = $request['groupName_' . $a];

            if ($request['chkCode_' . $a] == "Y") {
                $data = GroupCode::where('group_code', '=', $groupCode)
                    ->where('branch_code', '=', $this->admin_data['login_user']->branch_code)
                    ->first();
                $checkGroupCode = isset($data->group_code) ? $data->group_code : "";
                if ($checkGroupCode == "") {
                    GroupCode::create(['group_code' => $groupCode, 'group_name' => $groupName, 'branch_code' => $this->admin_data['login_user']->branch_code]);
                }
            } else {
//                DB::table('group_para')->where('group_code', '=', $groupCode)->where('branch_code', '=', $this->admin_data['login_user']->branch_code)->delete();
                $group_para = GroupCode::where('group_code', '=', $groupCode)->where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
//                dd($group_para);
                if($group_para)
                $group_para->delete();
            }
        }
        Session::flash('successMsg', 'Group Code Changed successfully');
        return response()->json(['success' => true, 'message' => 'Group Code successfully'], 200);
    }

    public function groupDetailIndex()
    {

        $this->admin_data['stmtgroup'] = GroupCode::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->orderBy('group_name')->get();
//        $query = "SELECT LOCATION_CODE, INITCAP(LOCATION_NAME) LOCATION_NAME FROM LH
//			WHERE LOCATION_CODE NOT IN (SELECT LOCATION_CODE FROM GRP WHERE BRANCH_CODE = '".$_SESSION['branchCode']."')
//			ORDER BY LOCATION_NAME";

//        $this->admin_data['stmtLocation'] = DB::select(DB::raw("SELECT location_code,location_name FROM location_hierarchy
//			WHERE location_code NOT IN (SELECT location_code FROM group_para WHERE branch_code = '" . $this->admin_data['login_user']->branch_code . "')
//			ORDER BY location_name"));
        $this->admin_data['stmtLocation'] = LocationHierarachy::orderBy('location_name', 'ASC')->get();

        return view('admin.group.groupDetail', $this->admin_data);
    }

    public function ajaxGetLocation(Request $request)
    {
        $group_locations = DB::table('group_para')->selectRaw('location_code,GET_LOCATION_NAME(location_code) as location_name')->where([
            ['group_para.group_code', '=', $request->group_code],
            ['group_para.branch_code', '=', $this->admin_data['login_user']->branch_code]
        ])->get();
        Session::put('group_locations', $group_locations);
        $group_location_html = view('admin.group.ajaxGroupLocation', compact('group_locations'))->render();
        return response()->json(['success' => true, 'message' => 'Location Successfully added', 'data' => ['group_location_html' => $group_location_html]], 200);
        $query = "SELECT LOCATION_CODE, INITCAP(GET_LOCATION_NAME(LOCATION_CODE)) LOCATION_NAME FROM GRP
			WHERE GROUP_CODE = '$groupCode'
				AND BRANCH_CODE = '" . $_SESSION['branchCode'] . "'
			ORDER BY LOCATION_NAME";
        $resGRP = $conn->selectdatafromtable($c, $query);
        $location =
        $a = 0;
        if (isset($resGRP)) {
            foreach ($resGRP as $data) {
                $arrayGRP[$a][0] = $data->LOCATION_CODE;
                $arrayGRP[$a][1] = $data->LOCATION_NAME;

                $a++;
            }
        }
    }

    public function ajaxLocationAdd(Request $request)
    {
        $group_locations = Session::get('group_locations');
        $added_location = LocationHierarachy::where('location_code', '=', $request->location_code)->first();

        $push = 'Y';
        $add = (object)[
            'location_code' => $added_location->location_code,
            'location_name' => $added_location->location_name
        ];
        foreach ($group_locations as $g) {
            if ($g->location_code == $request->location_code) {
                $push = 'N';
            }
        }
        if ($push == 'Y') {
            $group_locations->push($add);
        }

        Session::put('group_locations', $group_locations);
        $group_location_html = view('admin.group.ajaxGroupLocation', compact('group_locations'))->render();
        return response()->json(['success' => true, 'message' => 'Location Successfully added', 'data' => ['group_location_html' => $group_location_html]], 200);

    }

    public function groupDetailUpdate(Request $request)
    {
 //        $group_para=GroupPara::where('group_code','=',$request->group_code)->first();
        $group_locations = Session::get('group_locations');
        foreach ($group_locations as $g) {
            $query_group = GroupPara::where([
                ['group_code', '=', $request->group_code],
                ['location_code', '=', $g->location_code],
                ['branch_code', '=', $this->admin_data['login_user']->branch_code]
            ])->first();
            $branch_para = BranchPara::where('group_code', '=', $request->group_code)->first();

            if (!isset($query_group)) {
                GroupPara::create(['group_code' => $request->group_code, 'branch_code' => $this->admin_data['login_user']->branch_code, 'group_name' => $branch_para->group_name, 'location_code' => $g->location_code]);
            } else {
                GroupPara::where([
                    ['group_code', '=', $request->group_code],
                    ['location_code', '=', $g->location_code],
                    ['branch_code', '=', $this->admin_data['login_user']->branch_code]
                ])->update(['group_code' => $request->group_code, 'branch_code' => $this->admin_data['login_user']->branch_code, 'group_name' => $branch_para->group_name, 'location_code' => $g->location_code]);

            }
        }
        $stmtLocation = DB::select(DB::raw("SELECT location_code,location_name FROM location_hierarchy 
			WHERE location_code NOT IN (SELECT location_code FROM group_para WHERE branch_code = '" . $this->admin_data['login_user']->branch_code . "')
			ORDER BY location_name"));
        $location_html = view('admin.group.location_render', compact('stmtLocation'))->render();
        return response()->json(['success' => true, 'message' => 'Location Successfully added','data'=>['location_html'=>$location_html]], 200);


    }

    public function groupDetailDelete(Request $request)
    {
        $group_locations = Session::get('group_locations');
        foreach ($group_locations as $key => $g) {
            if ($g->location_code == $request->location_code) {
                $group_locations->forget($key);
                break;
            }
        }

        $group_para = GroupPara::where([
            ['location_code', '=', $request->location_code],
            ['group_code', '=', $request->group_code]
        ])->first();
        $group_para->delete();
        $group_location_html = view('admin.group.ajaxGroupLocation', compact('group_locations'))->render();
        $stmtLocation = DB::select(DB::raw("SELECT location_code,location_name FROM location_hierarchy 
			WHERE location_code NOT IN (SELECT location_code FROM group_para WHERE branch_code = '" . $this->admin_data['login_user']->branch_code . "')
			ORDER BY location_name"));
        $location_html = view('admin.group.location_render', compact('stmtLocation'))->render();
        return response()->json(['success' => true, 'message' => 'Location Successfully added', 'data' => ['group_location_html' => $group_location_html,'location_html'=>$location_html]], 200);

    }

    public function import(Request $request)
    {
        if (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function ($reader) {
            })->get();
            importCsv($data, 'route_para');
        }
        Session::flash('successMsg', 'Successfully Imported');
        return back();

    }

}
