<?php

namespace App\Http\Controllers;

use App\Models\Admin\EmployeeParameter;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;
use Session;


class JwtAuthenticateController extends Controller
{

    public function index()
    {
        return response()->json(['auth'=>Auth::user(), 'users'=>User::all()]);
    }
    public function appLogin(){
        Session::forget('branch_code');
        return view('Authentication.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['success'=>false,'message' => 'Invalid token'], 200);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['success'=>false,'message' => 'Could not Create token'], 200);
        }
        $user=Auth::user();
        $role=$user->roles()->get();
        $data=[
            'token'=>$token,
            'user_data'=>$user,
            'role'=>$role
        ];

        // if no errors are encountered we can return a JWT
        return response()->json(['success'=>true,'message'=>'Login Successfully','data'=>$data],200);
    }
    public function postLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['success'=>false,'message' => 'Invalid token'], 200);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['success'=>false,'message' => 'Could not Create token'], 200);
        }
        $user=Auth::user();
        $role=$user->roles()->get();
        $employee=EmployeeParameter::where('user_id','=',Auth::user()->id)->leftJoin('branch_paras as b','b.branch_code','=','employee_information_para.branch_code')->first();
        $data=[
            'token'=>$token,
            'user_data'=>$user,
            'role'=>$role,
            'branch_code'=>$employee->branch_code,
            'branch_name'=>$employee->branch_name
        ];

        // if no errors are encountered we can return a JWT
        return response()->json(['success'=>true,'message'=>'Login Successfully','data'=>$data],200);
    }

    public function createRole(Request $request){
        // Todo
        $role = new Role();
        $role->name = $request->input('name');
        $role->save();

        return response()->json("created");
    }

    public function createPermission(Request $request){
        // Todo
        $viewUsers = new Permission();
        $viewUsers->name = $request->input('name');
        $viewUsers->save();

        return response()->json("created");
    }

    public function assignRole(Request $request){
        // Todo
        $user = User::where('email', '=', $request->input('email'))->first();

        $role = Role::where('name', '=', $request->input('role'))->first();
        //$user->attachRole($request->input('role'));
        $user->roles()->attach($role->id);

        return response()->json("created");
    }

    public function attachPermission(Request $request){
        $role = Role::where('name', '=', $request->input('role'))->first();
        $permission = Permission::where('name', '=', $request->input('name'))->first();
        $role->attachPermission($permission);

        return response()->json("created");
    }
    public function logout(Request $request) {
        $this->validate($request, ['token' => 'required']);
        try {
            JWTAuth::invalidate($request->input('token'));
            return redirect('/app/login');
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

}