<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\BranchPara;
use App\Models\Admin\EmployeeParameter;
use Auth;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;
use Session;
use Illuminate\Support\Facades\Input;



class DashboardController extends Controller
{
  protected $admin_data;
    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->admin_data['login_user'] = Auth::user()->userData()->first();
            return $next($request);
        });
    }
         public function index(){
             if(Input::has('token')){
                 Session::put('token',Input::get('token'));
             }

//             $user_data=EmployeeParameter::where('user_id','=',$this->admin_data['login_user']->id)->first();
//             Session::put('branch_code',$user_data);
             $this->admin_data['token']=Session::get('token');
             $this->admin_data['branch_para']=BranchPara::orderBy('branch_name','asc')->get();
                    return view('admin.dashboard',$this->admin_data);
         }
         public function selectBranch(Request $request){
          Session::put('branch_code',$request->branch_code);
          Session::flash('successMsg','Branch successfully changed');
          return response()->json(['success'=>true,'message'=>'Branch successfully changed','data'=>null],200);
         }
}