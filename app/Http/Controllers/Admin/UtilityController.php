<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\EmployeeParameter;
use App\Models\Admin\LocationHierarachy;
use App\Models\Admin\CountryPara;
use App\Models\Admin\MerchandisePara;
use App\Models\Admin\RoleUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BillStockPara;
use App\Models\Admin\BranchPara;
use Session;
use DB;
use Auth;
use App\User;
use Hash;
use App\Role;
use Input;
use Excel;

class UtilityController extends DashboardController
{
    public function __construct()
    {

        parent::__construct();
    }

    public function billIssueIndex()
    {
        $this->admin_data['branch_paras'] = BranchPara::orderBy('BRANCH_CODE', 'desc')->get();
        $this->admin_data['bill_issues'] = BillStockPara::all();
        return view('admin.utility.bill_issue', $this->admin_data);
    }

    public function billIssueStore(Request $request)
    {
        if (!$request->isse_id) {
            $billissue_check = BillStockPara::where('prefix', '=', $request->prefix)
                ->whereRaw($request->bill_no_from.'>=bill_no_from')
                ->whereRaw($request->bill_no_from.'<=bill_no_to')
                ->first();
            $billissue_to = BillStockPara::where('prefix', '=', $request->prefix)
                ->where('bill_no_from', '>=', $request->bill_no_to)
                ->where('bill_no_to', '<=', $request->bill_no_to)
                ->first();
            if ($billissue_check || $billissue_to) {
                return response()->json(['success' => false, 'message' => 'Bill already taken'], 200);
            }
            $data = $request->all();
            $query = BillStockPara::select(DB::raw("MAX(CAST(SUBSTRING(issue_id, 6) AS UNSIGNED)+1) AS Issue"))->first();
          if($query== null){
              $data['issue_id']=1;
          }else
            $data['issue_id'] = date('Y') . '-' . $query->Issue;
            BillStockPara::create($data);
            Session::flash('successMsg', $data['issue_id']. ' has been generated and bill issue saved successfully');
        }
        return response()->json(['success' => true, 'message' => 'Bill issue saved successfully', 'data' => ['issue_id' => $data['issue_id']]], 200);

    }

    public function countryParaIndex()
    {
        $this->admin_data['country_paras'] = CountryPara::orderBy('id', 'desc')->get();
        return view('admin.utility.country_para', $this->admin_data);
    }

    public function countryParaStore(Request $request)
    {
        $data = $request->all();
        CountryPara::create($data);
        Session::flash('successMsg', 'Country parameter added successfully');
        return response()->json(['success' => true, 'message' => 'Country parameter added successfully'], 200);

    }


    public function locationHierIndex()
    {
        $this->admin_data['locations'] = LocationHierarachy::orderBy('id', 'desc')->get();
        return view('admin.utility.location_hier', $this->admin_data);
    }

    public function locationHierStore(Request $request)
    {
        $fillable = [
            'location_code', 'location_name', 'master_location_code', 'category', 'location_type', 'branch_name', 'contact_name', 'contact_number', 'email'
        ];
        foreach ($fillable as $f) {
            $data[$f] = ($request[$f] != null) ? $request[$f] : '';
        }
        LocationHierarachy::create($data);
        Session::flash('successMsg', 'Location has been added successfully');
        return response()->json(['success' => true, 'message' => 'Location added successfully'], 200);

    }

    public function import(Request $request)
    {

        if (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function ($reader) {
            })->get();
            importCsv($data, 'location_hierarchy');
        }
        Session::flash('successMsg', 'Successfully Imported');
        return back();

    }

    public function merchandiseIndex()
    {
        $this->admin_data['merchandise_para'] = MerchandisePara::orderBy('id', 'desc')->get();
        return view('admin.utility.merchandise_para', $this->admin_data);
    }

    public function merchandiseStore(Request $request)
    {
        MerchandisePara::create($request->all());
        Session::flash('successMsg', 'Merchandise Parameter has been added successfully');
        return response()->json(['success' => true, 'message' => 'Merchandise Parameter added successfully'], 200);

    }

    public function employeeIndex()
    {
        $this->admin_data['branch_paras'] = BranchPara::all();
        $this->admin_data['users'] = EmployeeParameter::join('users', 'users.id', '=', 'employee_information_para.user_id')->orderBy('employee_information_para.id', 'desc')->get();
        return view('admin.utility.employee_information', $this->admin_data);
    }

    public function employeeStore(Request $request)
    {
        DB::beginTransaction();
        if (!isset($request->currently_on_job)) {
            $con = 'N';
        } else {
            $con = 'Y';
        }

        try {
            $user = User::create(['name' => $request->employee_name, 'email' => $request->email, 'password' => Hash::make($request->password)]);

            $role = Role::where('id', '=', $request->user_type)->first();
            //$user->attachRole($request->input('role'));
            $user->roles()->attach($role->id);
            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['currently_on_job'] = $con;
            EmployeeParameter::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('successMsg', $e->getMessage());
            dd($e->getMessage());
            // something went wrong
        }
        Session::flash('successMsg', 'Employee Added successfully');
        return response()->json(['success' => true, 'message' => 'Employee generated successfully'], 200);
    }

    public function checkEmail(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();
        if (isset($user)) {
            return response()->json(['success' => false, 'message' => 'Email already taken.Please user another email'], 200);
        }
        return response()->json(['success' => true, 'message' => 'You can use this email'], 200);

    }

    public function edit($id)
    {
        $location_info = LocationHierarachy::find($id);
        $location_info_html = view('admin.utility.ajaxEditLocation', compact('location_info'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['location_info_html' => $location_info_html]], 200);

    }

    public function update_location(Request $request, $id)
    {
        $location = LocationHierarachy::find($id);
        $location->update($request->all());
        Session::flash('successMsg', 'Location Parameter updated successfully');
        return response()->json(['success' => true, 'message' => 'Location Parameter updated successfully'], 200);
    }

    public function edit_country($id)
    {
        $country_info = CountryPara::find($id);
        $country_info_html = view('admin.utility.ajaxEditCountry', compact('country_info'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['country_info_html' => $country_info_html]], 200);

    }

    public function update_country(Request $request, $id)
    {
        $location = CountryPara::find($id);
        $location->update($request->all());
        Session::flash('successMsg', 'Location Parameter updated successfully');
        return response()->json(['success' => true, 'message' => 'Location Parameter updated successfully'], 200);

    }

    public function edit_merchandise($id)
    {
        $merchandise_info = MerchandisePara::find($id);
        $merchandise_info_html = view('admin.utility.ajaxEditMerchandise', compact('merchandise_info'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['merchandise_info_html' => $merchandise_info_html]], 200);

    }

    public function update_merchandise(Request $request, $id)
    {
        $merchandise_info = MerchandisePara::find($id);
        $merchandise_info->update($request->all());
        Session::flash('successMsg', 'Merchandise Parameter updated successfully');
        return response()->json(['success' => true, 'message' => 'Merchandise Parameter updated successfully'], 200);

    }

    public function destroy_country($id)
    {
        $country_info = CountryPara::find($id);
        $country_info->delete();
        Session::flash('successMsg', 'Country Parameter deleted successfully');
        return response()->json(['success' => true, 'message' => 'Country Parameter deleted successfully'], 200);
    }

    public function destroy_merchandise($id)
    {
        $merchandise_info = MerchandisePara::find($id);
        $merchandise_info->delete();
        Session::flash('successMsg', 'Merchandise Parameter deleted successfully');
        return response()->json(['success' => true, 'message' => 'Merchandise Parameter deleted successfully'], 200);
    }

    public function destroy_location($id)
    {
        $location_info = LocationHierarachy::find($id);
        $location_info->delete();
        Session::flash('successMsg', 'Location Parameter deleted successfully');
        return response()->json(['success' => true, 'message' => 'Location Parameter deleted successfully'], 200);
    }

    public function edit_employee_information($id)
    {
        $branch_para = BranchPara::all();
        $employee_info = User::where('users.id', '=', $id)
            ->select('employee_information_para.*', 'users.*')
            ->join('employee_information_para', 'employee_information_para.user_id', '=', 'users.id')
            ->first();
        $employee_info_html = view('admin.utility.ajaxEditEmployee', compact('employee_info', 'branch_para'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['employee_info_html' => $employee_info_html]], 200);
    }

    public function update_employee_information(Request $request, $id)
    {
        $employee_info_update = User::where('id', '=', $id)->first();
        $user = User::where('id', '=', $id)->update(['name' => $request->employee_name, 'email' => $request->email]);
        DB::table('role_user')
            ->where('user_id', '=', $id)
            ->update(['role_id' => $request->user_type]);
        EmployeeParameter::where('user_id', '=', $id)->update(['branch_code' => $request->branch_code, 'currently_on_job' => $request->currently_on_job, 'address' => $request->address, 'mobile' => $request->mobile, 'phone' => $request->phone, 'fax' => $request->fax]);

        Session::flash('successMsg', 'Employee information updated successfully');
        return response()->json(['success' => true, 'message' => 'Employee information updated successfully'], 200);
    }

    public function destroy_employee_information(Request $request, $id)
    {
        $employee_info_delete = User::find($id);
        User::where('id', '=', $id)->delete();
        EmployeeParameter::where('user_id', '=', $id)->delete();
        DB::table('role_user')
            ->where('user_id', '=', $id)
            ->delete();

        Session::flash('successMsg', 'Employee Information has been deleted successfully');
        return response()->json(['success' => true, 'message' => 'Employee Information has been deleted successfully'], 200);

    }

    public function changePasswordIndex()
    {
//        $this->admin_data['user']=User::where('email','=',$request->email)->first();
        return view('admin.utility.change_password', $this->admin_data);
    }

    public function changePassword(Request $request)
    {

        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            return redirect()->back()->with('error', 'Your current password doesnot matches with the password you provided.Please try again');
        }
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");

        }
        $this->validate($request, [
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);

        //Change password

        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return redirect()->back()->with("success", "Password changed successfully !");


    }
}
