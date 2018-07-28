<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\BillsRecord;
use App\Models\Admin\CashBook;
use App\Models\Admin\Company;
use App\Models\Admin\CountryPara;
use App\Models\Admin\CustomerDetail;
use App\Models\Admin\CustomerPara;
use App\Models\Admin\CustomerPriceDetail;
use App\Models\Admin\EmployeeParameter;
use App\Models\Admin\Member;
use App\Models\Admin\MerchandisePara;
use App\Models\Admin\Notifications;
use App\Models\Admin\Package;
use App\Models\Admin\ZoneMaster;
use App\Permission;
use App\RolePermission;
use App\UserPermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\User;
use App\Role;
use Session;
use Hash;
use App\Models\Admin\LocationHierarachy;
use App\Models\Admin\BranchPara;
use Input;
use Excel;
use App;
use Illuminate\Support\Collection;
use  App\Repositories\Backend\BranchPara\BranchParaInterface;

class UserController extends DashboardController
{
    public function __construct(BranchParaInterface $branch_para)
    {
        parent::__construct();
        $this->branch_para = $branch_para;
    }

    public function customerIndex()
    {

        Session::forget('function_type_b');
        Session::forget('function_type_a');
        $this->admin_data['customer_paras'] = CustomerPara::orderBy('id', 'desc')->where('branch_code', '=', $this->admin_data['login_user']->branch_code)->get();
        $this->admin_data['master_companies'] = Company::all();
        $this->admin_data['country_para'] = CountryPara::orderBy('country_name', 'desc')->get();
        $this->admin_data['branch_para'] = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
        return view('admin.user.customer.index', $this->admin_data);
    }

    public function package()
    {
        $this->admin_data['packages'] = Package::orderby('id', 'desc')->get();
        return view('admin.package.index', $this->admin_data);
    }

    public function packageStore(Request $request)
    {
        Package::create($request->all());
        Session::flash('successMsg', 'Package saved successfully');
        return response()->json(['success' => 'true'], 200);
    }

    public function selectedPackage(Request $request)
    {
        if ($request->month == null)
            return response()->json(['success' => false], 200);
        $month = Package::where('month', '=', $request->month)->first();
        $time = strtotime($request->admission_date);
        $user_valid_date = date("Y-m-d", strtotime("+" . $request->month . " month", $time));
        return response()->json(['success' => true, 'message' => '', 'data' => ['price' => $month->price, 'user_valid_date' => $user_valid_date]], 200);
    }


    public function selectedfunctionType(Request $request)
    {

        $function_type = $request->function_type;
        $zones = ZoneMaster::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->get();
        $locations = LocationHierarachy::where('location_type', '=', 'PLC')->orderBy('location_name', 'asc')->get();
        $merchandise = MerchandisePara::all();
        $function_type_html = view('admin.user.customer.function_type', compact('function_type', 'zones', 'locations', 'merchandise'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['function_type_html' => $function_type_html]], 200);
    }

    public function editSelectedfunctionType(Request $request)
    {
        if ($request->function_type == 'B') {
            $customer_prices = CustomerPriceDetail::select('location_code')->where('customer_code', '=', $request->customer_code)->groupBy('location_code')->get();
            $function_type_b = new Collection();

            foreach ($customer_prices as $c) {
                $d['location_code'] = $c->location_code;
                $d['DOX_S'] = doxS($c->location_code, $request->customer_code);
                $d['NDx_S'] = ndxS($c->location_code, $request->customer_code);
                $d['DOX_A'] = doxA($c->location_code, $request->customer_code);
                $d['NDx_A'] = ndxA($c->location_code, $request->customer_code);
                $add = (object)$d;
                $function_type_b->push($add);
            }
            Session::put('function_type_b', $function_type_b);
        } elseif ($request->function_type == 'A') {
            Session::forget('function_type_b');
            $customer_prices = CustomerDetail::where('customer_code', '=', $request->customer_code)->get();
            $function_type_a = new Collection();

            foreach ($customer_prices as $c) {
                $d['zone'] = $c->zone_code;
                $d['discount'] = $c->discount_pct;
                $add = (object)$d;
                $function_type_a->push($add);
            }
            Session::put('function_type_a', $function_type_a);
        }


        $function_type = $request->function_type;

        $zones = ZoneMaster::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->get();
        $locations = LocationHierarachy::where('location_type', '=', 'PLC')->orderBy('location_name', 'asc')->get();
        $merchandise = MerchandisePara::all();
        $function_type_html = view('admin.user.customer.edit_function_type', compact('function_type', 'function_type_b', 'function_type_a', 'zones', 'locations', 'merchandise'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['function_type_html' => $function_type_html]], 200);
    }

    public function addPrice(Request $request)
    {
        if ($request->function_type == 'B') {
            $function_type_b = Session::get('function_type_b');
            if (!isset($function_type_b))
                $function_type_b = new Collection();
            $push = 'Y';

            $add = (object)$request->all();
            if (isset($function_type_b)) {

                foreach ($function_type_b as $b) {
                    if ($b->location_code == $request->location_code) {
                        $b->DOX_S = isset($request->DOX_S) ? $request->DOX_S : '';
                        $b->NDx_S = isset($request->NDx_S) ? $request->NDx_S : '';
                        $b->DOX_A = isset($request->DOX_A) ? $request->DOX_A : '';
                        $b->NDx_A = isset($request->NDx_A) ? $request->NDx_A : '';
                        $push = 'N';
                    }
                }
            }
            if ($push == 'Y') {
                $function_type_b->push($add);
            }
            Session::put('function_type_b', $function_type_b);


            $function_type_b_html = view('admin.user.customer.function_type_b_html', compact('function_type_b'))->render();
            return response()->json(['success' => true, 'message' => 'function type b added', 'data' => ['function_type_b_html' => $function_type_b_html]], 200);
        } elseif ($request->function_type == 'A') {
            $function_type_a = Session::get('function_type_a');
            if (!isset($function_type_a))
                $function_type_a = new Collection();
            $push = 'Y';

            $add = (object)$request->all();
            if (isset($function_type_a)) {

                foreach ($function_type_a as $b) {
                    if ($b->zone == $request->zone) {
                        $push = 'N';
                    }
                }
            }
            if ($push == 'Y') {
                $function_type_a->push($add);
            }
            Session::put('function_type_a', $function_type_a);


            $function_type_a_html = view('admin.user.customer.function_type_a_html', compact('function_type_a'))->render();
            return response()->json(['success' => true, 'message' => 'function type a added', 'data' => ['function_type_a_html' => $function_type_a_html]], 200);

        }
    }

    public function customerStore(Request $request)
    {
        DB::beginTransaction();
        $query = CustomerPara::select(DB::raw("MAX(CAST(SUBSTRING(shipper_code, 6) AS UNSIGNED)+1) AS Shipper_code"))->first();
        if ($query->Shipper_code != null) {
            $shipper_code = 'S' . $this->admin_data['login_user']->branch_code . '-' . $query->Shipper_code;
        } else {
            $shipper_code = 'S' . $this->admin_data['login_user']->branch_code . '-' . '1';
        }
        if (!isset($request->usedTag)) {
            $usedTag = 'N';
        } else {
            $usedTag = 'Y';
        }
        if (!isset($request->cod)) {
            $cod = 'No';
        } else {
            $cod = 'Yes';
        }
        $query = CustomerPara::select(DB::raw("MAX(CAST(SUBSTRING(customer_code, 5) AS UNSIGNED)+1) AS Customer_code"))->first();
        if ($query->Customer_code != null) {
            $customer_code = 'C' . $this->admin_data['login_user']->branch_code . $query->Customer_code;
        } else {
            $customer_code = 'C' . $this->admin_data['login_user']->branch_code . '1';
        }

        try {
            if (isset($request->email) && isset($request->password)) {
                $user = User::create(['name' => $request->customer_name, 'email' => isset($request->email) ? $request->email : '', 'password' => isset($request->password) ? Hash::make($request->password) : '']);

                $role = Role::where('name', '=', 'customer')->first();
                //$user->attachRole($request->input('role'));
                $user->roles()->attach($role->id);
            }

            $customer = [
                'user_id' => isset($user->id) ? $user->id : '',
                'company_code' => $request->company_code,
                'customer_code' => $customer_code,
                'customer_name' => $request->customer_name,
                'country_code' => $request->country_code,
                'phone' => isset($request->phone) ? $request->phone : '',
                'shipper_code' => $shipper_code,
                'shipper_name' => $request->shipper_name,
                'fax' => isset($request->fax) ? $request->fax : '',
                'address' => isset($request->address) ? $request->address : '',
                'mobile' => isset($request->mobile) ? $request->mobile : '',
                'email' => isset($request->email) ? $request->email : '',
                'function_type' => isset($request->function_type) ? $request->function_type : '',
                'used_tag' => $usedTag,
                'tracking_report' => isset($request->trackingReport) ? $request->trackingReport : 'N',
                'vat_applicable' => $request->vat_applicable,
                'vat_no' => isset($request->vat_no) ? $request->vat_no : '',
                'ac_code' => isset($request->ac_code) ? $request->ac_code : '',
                'branch_code' => $this->admin_data['login_user']->branch_code,
                'delivery_hrs' => isset($request->delivery_hrs) ? $request->delivery_hrs : '',
                'cod' => $cod,
                'payment_type' => isset($request->payment_type) ? $request->payment_type : ''
            ];
            $check_customer = CustomerPara::where('customer_code', '=', $request->customer_code)->first();
            if (!$check_customer)
                CustomerPara::create($customer);
            else {
                $customer_code = $request->customer_code;
                $customer = [
                    'user_id' => isset($user->id) ? $user->id : '',
                    'company_code' => $request->company_code,
                    'customer_name' => $request->customer_name,
                    'country_code' => $request->country_code,
                    'phone' => isset($request->phone) ? $request->phone : '',
                    'shipper_name' => $request->shipper_name,
                    'fax' => isset($request->fax) ? $request->fax : '',
                    'address' => isset($request->address) ? $request->address : '',
                    'mobile' => isset($request->mobile) ? $request->mobile : '',
//                    'email' => isset($request->email)?$request->email:'',
                    'function_type' => isset($request->function_type) ? $request->function_type : '',
                    'used_tag' => $usedTag,
                    'tracking_report' => isset($request->trackingReport) ? $request->trackingReport : 'N',
                    'vat_applicable' => $request->vat_applicable,
                    'vat_no' => isset($request->vat_no) ? $request->vat_no : '',
                    'ac_code' => isset($request->ac_code) ? $request->ac_code : '',
                    'branch_code' => $this->admin_data['login_user']->branch_code,
                    'delivery_hrs' => isset($request->delivery_hrs) ? $request->delivery_hrs : '',
                    'cod' => $cod,
                    'payment_type' => isset($request->payment_type) ? $request->payment_type : ''
                ];
                CustomerPara::where('customer_code', '=', $request->customer_code)->update($customer);
            }
            if ($request->function_type == 'A') {
                foreach ($request->zone as $key => $z) {
                    $c_z_price_check = CustomerDetail::where('zone_code', '=', $z)->where('customer_code', '=', $customer_code)->first();
                    if (!$c_z_price_check)
                        CustomerDetail::create(['zone_code' => $z, 'customer_code' => $customer_code, 'discount_pct' => $request['discount'][$key]]);
                    else
                        CustomerDetail::where('zone_code', '=', $z)->where('customer_code', '=', $customer_code)->update(['discount_pct' => $request['discount'][$key]]);
                }
            } elseif ($request->function_type == 'B') {
                foreach ($request->location_code as $key => $l) {
                    $merchandise = MerchandisePara::all();
                    $mailing_mode = ['S' => 'Surface', 'A' => 'Air'];
                    foreach ($merchandise as $m) {
                        foreach ($mailing_mode as $key2 => $mail) {
                            if (isset($request[$m->merchandise_code . '_' . $key2][$key])) {
                                $price_data = [
                                    'customer_code' => $customer_code,
                                    'effective_date_from' => isset($request->effective_from) ? $request->effective_from : null,
                                    'effective_date_to' => isset($request->effective_to) ? $request->effective_to : null,
                                    'mailing_mode' => $key2,
                                    'merchandise_type' => $m->merchandise_code,
                                    'location_code' => $l,
                                    'rate' => $request[$m->merchandise_code . '_' . $key2][$key],
                                    'remarks' => isset($request['remarks'][$key]) ? $request['remarks'][$key] : ''
                                ];
                                $customer_price_check = CustomerPriceDetail::where('mailing_mode', $key2)
                                    ->where('merchandise_type', '=', $m->merchandise_code)
                                    ->where('location_code', '=', $l)
                                    ->first();
                                if (!$customer_price_check) {
                                    CustomerPriceDetail::create($price_data);
                                } else {
                                    CustomerPriceDetail::where('mailing_mode', $key2)
                                        ->where('merchandise_type', '=', $m->merchandise_code)
                                        ->where('location_code', '=', $l)->update($price_data);
                                }
                            }
                        }
                    }

                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('successMsg', $e->getMessage());
            dd($e->getMessage());
            // something went wrong
        }
        if ($check_customer) {
            Session::flash('successMsg', 'Updated successfully');

            return response()->json(['success' => true, 'message' => 'Updated Successfully'], 200);
        } else {
            Session::flash('successMsg', 'Shipper code ' . $shipper_code . 'and customer code ' . $customer_code . ' generated successfully');

            return response()->json(['success' => true, 'message' => 'Customer added successfully.' . 'Shipper code ' . $shipper_code . 'and customer code ' . $customer_code . ' generated successfully'], 200);
        }
    }

    public function edit_customer_info($id)
    {
        $this->admin_data['customer_info'] = CustomerPara::find($id);
        $this->admin_data['master_companies'] = Company::all();
        $this->admin_data['country_para'] = CountryPara::orderBy('country_name', 'desc')->get();
        $this->admin_data['branch_para'] = BranchPara::where('branch_code', '=', $this->admin_data['login_user']->branch_code)->first();
//        $customer_info_html = view('admin.user.customer.ajaxEdit', compact('customer_info'))->render();
//        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['customer_info_html' => $customer_info_html]], 200);
//dd($this->admin_data);
        return view('admin.user.customer.edit', $this->admin_data);
    }

    public function update_customer_info(Request $request, $id)
    {
        dd($request->all());
        $customer = CustomerPara::find($id);
        $customer->update($request->all());
        Session::flash('successMsg', 'Customer Parameter updated successfully');
        return response()->json(['success' => true, 'message' => 'Customer Parameter updated successfully'], 200);

    }

    public function destroy_customer($id)
    {
        $customer = CustomerPara::find($id);
        $customer->delete();
        Session::flash('successMsg', 'Customer Parameter has been deleted successfully');
        return response()->json(['success' => true, 'message' => 'Customer Parameter has been deleted successfully'], 200);


    }

    public function customerExcelStore(Request $request)
    {

        if (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function ($reader) {
            })->get();
            DB::beginTransaction();
            foreach ($data as $d) {

                $query = CustomerPara::select(DB::raw("MAX(CAST(SUBSTRING(shipper_code, 6) AS UNSIGNED)+1) AS Shipper_code"))->first();
                if ($query->Shipper_code != null) {
                    $shipper_code = 'S' . $this->admin_data['login_user']->branch_code . '-' . $query->Shipper_code;
                } else {
                    $shipper_code = 'S' . $this->admin_data['login_user']->branch_code . '-' . '1';
                }
                if (!isset($request->usedTag)) {
                    $usedTag = 'N';
                } else {
                    $usedTag = 'Y';
                }
                if (!isset($request->cod)) {
                    $cod = 'No';
                } else {
                    $cod = 'Yes';
                }
                $query = CustomerPara::select(DB::raw("MAX(CAST(SUBSTRING(customer_code, 5) AS UNSIGNED)+1) AS Customer_code"))->first();
                if ($query->Customer_code != null) {
                    $customer_code = 'C' . $this->admin_data['login_user']->branch_code . $query->Customer_code;
                } else {
                    $customer_code = 'C' . $this->admin_data['login_user']->branch_code . '1';
                }

                try {
                    $user = User::create(['name' => $d->customer_name, 'email' => $d->email, 'password' => Hash::make($d->password)]);

                    $role = Role::where('name', '=', 'customer')->first();
                    //$user->attachRole($d->input('role'));
                    $user->roles()->attach($role->id);

                    $customer = [
                        'user_id' => $user->id,
                        'company_code' => $d->company_code,
                        'customer_code' => $customer_code,
                        'customer_name' => isset($d->customer_name) ? $d->customer_name : '',
                        'country_code' => $d->country_code,
                        'phone' => $d->phone != null ? $d->phone : '',
                        'shipper_code' => $shipper_code,
                        'shipper_name' => $d->shipper_name != null ? $d->shipper_name : '',
                        'fax' => $d->fax != null ? $d->fax : '',
                        'address' => $d->address != null ? $d->address : '',
                        'mobile' => $d->mobile != null ? $d->mobile : '',
                        'email' => $d->email,
                        'function_type' => $d->function_type,
                        'used_tag' => $usedTag,
                        'tracking_report' => $d->trackingReport != null ? $d->trackingReport : 'N',
                        'vat_applicable' => $d->vat_applicable != null ? $d->vat_applicable : '',
                        'vat_no' => $d->vat_no != null ? $d->vat_no : '',
                        'ac_code' => $d->ac_code != null ? $d->ac_code : '',
                        'branch_code' => $this->admin_data['login_user']->branch_code,
                        'delivery_hrs' => $d->delivery_hrs != null ? $d->delivery_hrs : '',
                        'cod' => $cod,
                        'payment_type' => $d->payment_type
                    ];
                    CustomerPara::create($customer);
//                        if ($d->function_type == 'A') {
//                            $zone=explode(",",$d->zone);
//                            $discount=explode(",",$d->discount);
//                            foreach ($zone as $key => $z) {
//                                CustomerDetail::create(['zone_code' => $z, 'user_id' => $user->id, 'customer_code' => $customer_code, 'discount_pct' => $discount[$key]]);
//                            }
//                        } else {
//                            $location_code=explode(",",$d->location_code);
//                           $t['remarks']=explode(",",$d->remarks);
//                           $t['effective_from']=explode(",",$d->effective_from);
//                           $t['effective_to']=explode(",",$d->effective_to);
//                           $t['DOX_S']=explode(",",$d->dox_s);
//                           $t['DOX_A']=explode(",",$d->dox_a);
//                           $t['NDx_S']=explode(",",$d->ndx_s);
//                           $t['NDx_A']=explode(",",$d->ndx_a);
//                            foreach ($location_code as $key => $l) {
//                                $merchandise = MerchandisePara::all();
//                                $mailing_mode = ['S' => 'Surface', 'A' => 'Air'];
//                                foreach ($merchandise as $m) {
//                                    foreach ($mailing_mode as $key2 => $mail) {
//                                        if ($t[$m->merchandise_code . '_' . $key2][$key] != null) {
//                                            $price_data = [
//                                                'user_id' => $user->id,
//                                                'customer_code' => $customer_code,
//                                                'effective_date_from' =>'2074-04-10',
//                                                'effective_date_to' => '2074-04-10',
//                                                'mailing_mode' => $key2,
//                                                'merchandise_type' => $m->merchandise_code,
//                                                'location_code' => $l,
//                                                'rate' => $t[$m->merchandise_code . '_' . $key2][$key],
//                                                'remarks' => $t['remarks'][$key]
//                                            ];
//                                            CustomerPriceDetail::create($price_data);
//
//                                        }
//                                    }
//                                }
//
//                            }
//                        }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    Session::flash('successMsg', $e->getMessage());
                    dd($e->getMessage());
                    // something went wrong
                }


            }
            Session::flash('sucessMsg', 'customer saved succssfully');
            return response()->json(['success' => true, 'message' => 'Customer Saved successfully', 'data' => null], 200);


        }
    }

    public function rolePermission()
    {
        return view('admin.user.role.permission', $this->admin_data);
    }

    public function selectedRole(Request $request)
    {
        $this->admin_data['attribute'] = $request->all();
        $role = Role::where('id', '=', $request->userType)->first();
        $this->admin_data['role_permissions'] = $role->perms()->get();
        $this->admin_data['all_permission'] = Permission::all();
        $role_permission_html = view('admin.user.role.ajaxPermission', $this->admin_data)->render();
        return response()->json(['success' => true, 'message' => 'Role Permission', 'data' => ['role_permission_html' => $role_permission_html]], 200);
    }

    public function storePermission(Request $request)
    {
        $role = Role::where('id', '=', $request->userType)->first();
        $this->admin_data['role_permissions'] = $role->perms()->get();
        $permission = Permission::all();
        foreach ($permission as $p) {
            if (isset($request[$p->name])) {
                $role_permission = RolePermission::where('role_id', '=', $request->userType)->where('permission_id', '=', $request[$p->name])->first();

                if (!$role_permission) {
                    RolePermission::insert(['role_id' => $request->userType, 'permission_id' => $request[$p->name]]);
                }
            } else {
                $permission = Permission::where('name', '=', $p->name)->first();
                DB::table('permission_role')->where('role_id', '=', $request->userType)
                    ->where('permission_id', '=', $permission->id)->delete();
//
            }

        }
        Session::flash('successMsg', 'User Type Permission updated successfully');
        return response()->json(['success' => true, 'message' => 'Permission updated successfully', 'data' => null], 200);
    }

    public function userPermissionIndex()
    {
        $this->admin_data['branch_para'] = BranchPara::all();
        return view('admin.user.permission', $this->admin_data);
    }

    public function userBranch(Request $request)
    {
        $this->admin_data['attribute'] = $request->all();
        $this->admin_data['branch_user'] = User::join('employee_information_para as e', 'e.user_id', '=', 'users.id')
            ->where('e.branch_code', '=', $request->branch_code)
            ->get();
        $user_html = view('admin.user.ajaxPermission', $this->admin_data)->render();
        return response()->json(['success' => true, 'message' => 'Branch User', 'data' => ['user_html' => $user_html]], 200);
    }

    public function userSelected(Request $request)
    {
        $this->admin_data['attribute'] = $request->all();
        $this->admin_data['user_permissions'] = UserPermission::where('permission_user.user_id', '=', $request->user)
            ->join('permissions as p', 'p.id', '=', 'permission_user.permission_id')
            ->get();
        $this->admin_data['all_permission'] = Permission::all();
        $user_permission_html = view('admin.user.selected', $this->admin_data)->render();
        return response()->json(['success' => true, 'message' => 'Role Permission', 'data' => ['user_permission_html' => $user_permission_html]], 200);
    }

    public function userStorePermission(Request $request)
    {
        $this->admin_data['user_permissions'] = UserPermission::where('permission_user.user_id', '=', $request->user)
            ->join('permissions as p', 'p.id', '=', 'permission_user.permission_id')
            ->get();
        $permission = Permission::all();
        foreach ($permission as $p) {
            if (isset($request[$p->name])) {
                $user_permission = UserPermission::where('user_id', '=', $request->user)->where('permission_id', '=', $request[$p->name])->first();
                if (!$user_permission) {
                    UserPermission::insert(['user_id' => $request->user, 'permission_id' => $request[$p->name]]);
                }
            } else {
                $permission = Permission::where('name', '=', $p->name)->first();
                DB::table('permission_user')->where('user_id', '=', $request->user)
                    ->where('permission_id', '=', $permission->id)->delete();
//
            }

        }
        Session::flash('successMsg', 'User Type Permission updated successfully');
        return response()->json(['success' => true, 'message' => 'Permission updated successfully', 'data' => null], 200);
    }

    public function deletePackage($id)
    {
        $package = Package::find($id);
        $package->delete();
        Session::flash('successMsg', 'Package deleted successfully');
        return response()->json(['success' => true, 'message' => 'Package Deleted', 'data' => null], 200);
    }

    public function editPackage($id)
    {
        $package = Package::find($id);
        return response()->json(['success' => true, 'message' => 'Package Editted', 'data' => ['package' => $package]], 200);
    }

    public function updatePackage(Request $request)
    {
        Package::where('id', '=', $request->package_id)->update(['month' => $request->month_pop, 'price' => $request->price_pop]);
//        $package =Package::find($id);
        Session::flash('successMsg', 'Package updated successfully');
        return response()->json(['success' => true, 'message' => 'Package Deleted', 'data' => null], 200);

    }

    public function billRecord()
    {
        $query = BillsRecord::select(DB::raw("MAX(bill_no)+1 AS bill_no"))->first();
        if ($query->bill_no != null) {
            $this->admin_data['bill_no'] = $query->bill_no;
        } else {
            $this->admin_data['bill_no'] = 1;
        }
        $this->admin_data['members'] = Member::all();
        return view('admin.bill_record.index', $this->admin_data);
    }

    public function storeBillRecord(Request $request)
    {
        $bil_record = new BillsRecord();
        $bil_record->date = isset($request->date) ? $request->date : '0';
        $bil_record->membership_no = isset($request->membership_no) ? $request->membership_no : '';
        $query = BillsRecord::select(DB::raw("MAX(bill_no)+1 AS bill_no"))->first();
        if ($query->bill_no != null) {
            $bil_record->bill_no = $query->bill_no;
        } else {
            $bil_record->bill_no = 1;
        }
        $bil_record->package = isset($request->package) ? $request->package : '0';
        $bil_record->amount = isset($request->amount) ? $request->amount : '0';
        $bil_record->discount = isset($request->discount) ? $request->discount : '0';
        $bil_record->paid_amount = isset($request->paid_amount) ? $request->paid_amount : '0';
        $bil_record->due_amount = isset($request->due_amount) ? $request->due_amount : '0';
        $bil_record->remarks = isset($request->remarks) ? $request->remarks : '';
        $bil_record->valid_date = isset($request->user_valid_date) ? $request->user_valid_date : '';
        $bil_record->save();

        $bil_record = new CashBook();
        $bil_record->date = isset($request->date) ? $request->date : '0';
        $bil_record->particular = isset($request->member_name) ? $request->member_name : '';
        $bil_record->debit_amount = isset($request->paid_amount) ? $request->paid_amount : '';
        $bil_record->credit_amount = '0.00';
        $bil_record->save();


        $member = Member::where('membership_no', '=', $request->membership_no)->first();
        if ($member) {
            $member->user_valid_date = $request->user_valid_date;
            $member->save();
        }


        Session::flash('successMsg', $bil_record->bill_no . ' Record Added successfully');
        return response()->json(['success' => true, 'message' => 'New Bill Record Added successfully', 'data' => null], 200);

    }

    public function listBillRecord()
    {
        $this->admin_data['bill_records'] = BillsRecord::select('members.*', 'bills_record.*')
            ->leftJoin('members', 'members.membership_no', '=', 'bills_record.membership_no')
            ->orderBy('bills_record.id', 'desc')
            ->get();
        return view('admin.bill_record.list', $this->admin_data);
    }

    public function addUser()
    {
        $query = Member::select(DB::raw("MAX(membership_no)+1 AS membership_no"))->first();
        if ($query->membership_no != null) {

            $this->admin_data['membership_no'] = $query->membership_no;
        } else {
            $this->admin_data['membership_no'] = 1;
        }
        $query = BillsRecord::select(DB::raw("MAX(bill_no)+1 AS bill_no"))->first();
        if ($query->bill_no != null) {
            $this->admin_data['bill_no'] = $query->bill_no;
        } else {
            $this->admin_data['bill_no'] = 1;
        }
        $this->admin_data['packages'] = Package::all();
        return view('admin.user.add', $this->admin_data);
    }

    public function storeUser(Request $request)
    {
        $member_check = Member::where('membership_no', '=', $request->membership_no)->first();
        if ($member_check) {
            return response()->json(['success' => true, 'message' => 'Already saved', 'data' => null], 200);

        }
        $data = $request->all();
        DB::beginTransaction();
        try {
            $input = [
                'membership_no', 'name', 'address', 'user_valid_date', 'gender', 'admission_date', 'package_rate', 'email', 'contact', 'photo', 'user_status'
            ];


            foreach ($input as $i) {
                $data[$i] = isset($data[$i]) ? $data[$i] : '';
            }

            $data['user_status'] = 'Active';
            Member::create($data);
            $bil_record = new BillsRecord();
            $bil_record->date = isset($request->admission_date) ? $request->admission_date : '';
            $bil_record->membership_no = isset($data['membership_no']) ? $data['membership_no'] : '';
            $bil_record->bill_no = isset($request['bill_no']) ? $request['bill_no'] : '';
            $bil_record->package = '';

            $bil_record->amount = isset($request->paid_amount) ? $request->paid_amount : '0';
            $bil_record->discount = isset($request->discount) ? $request->discount : '0';
            $bil_record->paid_amount = isset($request->paid_amount) ? $request->paid_amount : '0';
            $bil_record->due_amount = isset($request->due_amount) ? $request->due_amount : '0';
            $bil_record->remarks = 'Admission';
            $bil_record->save();

            $bil_record = new CashBook();
            $bil_record->date = isset($request->admission_date) ? $request->admission_date : '';
            $bil_record->particular = isset($request->name) ? $request->name : '';
            $bil_record->debit_amount = isset($request->paid_amount) ? $request->paid_amount : '';
            $bil_record->credit_amount = '0.00';
            $bil_record->save();


            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('successMsg', $e->getMessage());
            dd($e->getMessage());
            // something went wrong
        }
        Session::flash('successMsg', 'User added Successfully');

        return response()->json(['success' => true, 'message' => 'Successfully saved', 'data' => null], 200);
    }

    public function deleteBillRecord(Request $request)
    {
        $bill_record = BillsRecord::find($request->record_id);
        $bill_record->delete();
        Session::flash('successMsg', 'Bill Record Deleted Successfully');
        return response()->json(['success' => true, 'message' => "record deleted successfully", 'data' => null], 200);
    }

    public function editBillRecord($id)
    {

        $this->admin_data['members'] = Member::all();
        $this->admin_data['bill_record'] = BillsRecord::where('bills_record.id', '=', $id)->leftJoin('members as m', 'm.membership_no', '=', 'bills_record.membership_no')->first();
        return view('admin.bill_record.edit', $this->admin_data);

    }

    public function updateBillRecord(Request $request)
    {
        $bill_record = BillsRecord::where('bill_no', '=', $request->bill_no)
            ->first();
        $bill_record->date = isset($request->date) ? $request->date : '0';
        $bill_record->membership_no = isset($request->membership_no) ? $request->membership_no : '';
        $bill_record->package = isset($request->package) ? $request->package : '0';
        $bill_record->amount = isset($request->amount) ? $request->amount : '0';
        $bill_record->discount = isset($request->discount) ? $request->discount : '0';
        $bill_record->paid_amount = isset($request->paid_amount) ? $request->paid_amount : '0';
        $bill_record->due_amount = isset($request->due_amount) ? $request->due_amount : '0';
        $bill_record->remarks = isset($request->remarks) ? $request->remarks : '';
        $bill_record->save();

        Session::flash('successMsg', 'Bill record updated successfully');
        return response()->json(['success' => true, 'message' => 'Bill record updated successfully', 'data' => null], 200);
    }

    public function cashEntry()
    {
        return view('admin.user.cash_entry', $this->admin_data);

    }

    public function cashEntryPost(Request $request)
    {
        $book = new CashBook();
        $book->date = $request->date;
        $book->particular = isset($request->particular) ? $request->particular : '';
        $book->debit_amount = isset($request->debit_amount) ? $request->debit_amount : 0;
        $book->credit_amount = isset($request->credit_amount) ? $request->credit_amount : 0;
        $book->save();
        Session::flash('successMsg', 'Content saved successfully');
        return response()->json(['success' => true]);
    }

    public function cashEntryQuery(Request $request)
    {
        $this->admin_data['attribute'] = $request->all();
        $this->admin_data['cash_book'] = CashBook::whereBetween('date', [$request->date_from, $request->date_to])
            ->get();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.user.pdf', $this->admin_data)->render());
        return $pdf->setPaper('A4', 'portrait')->stream();

    }

    public function query()
    {
        return view('admin.user.query', $this->admin_data);
    }

    public function userList()
    {
        $this->admin_data['members'] = Member::orderby('id', 'desc')->get();
        return view('admin.user.list', $this->admin_data);
    }

    public function billDetail(Request $request, $membership_no)
    {
        $this->admin_data['bill_detail'] = BillsRecord::where('membership_no', '=', $membership_no)->get();
        return view('admin.user.bill_detail', $this->admin_data);

    }

    public function editUser($id)
    {
        $this->admin_data['user'] = Member::where('membership_no', '=', $id)->first();
        return view('admin.user.edit', $this->admin_data);
    }

    public function queryNotifications(Request $request)
    {
        $notifications = Notifications::where('id', '=', $request->id)->first();
        $notifications->status = 1;
        $notifications->save();
        return redirect()->route('admin.user.edit', ['id' => $notifications->reference_id, 'token' => Session::get('token')]);
    }

    public function memberSelected(Request $request)
    {
        $member = Member::where('membership_no', '=', $request->membership_no)->first();
        return response()->json(['success' => true, 'message' => 'member name', 'data' => ['name' => $member->name]]);
    }

    public function updateUser(Request $request)
    {

        $user = Member::where('membership_no', '=', $request->membership_no)->first();


//
//            $image = $request->file('image');
//        $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
//        $destinationPath = public_path('/images');
//        $image->move($destinationPath, $input['imagename']);
//        $this->postImage->add($input);

        $user->name = $request->name;
        $user->address = isset($request->address) ? $request->address : '';
        $user->user_valid_date = $request->user_valid_date;
        $user->gender = $request->gender;
        $user->admission_date = $request->admission_date;
        $user->package_rate = isset($request->package_rate) ? $request->package_rate : ' ';
        $user->email = isset($request->email) ? $request->email : '';
        $user->contact = isset($request->contact) ? $request->contact : '';
        $user->user_status = $request->user_status;
        $user->save();
        Session::flash('successMsg', 'User updated successfully');
        return response()->json(['success' => true, 'message' => 'User updated successfully', 'data' => null], 200);

    }

    public function viewNotifications()
    {
        $this->admin_data['all_not'] = Notifications::orderBy('id', 'desc')->get();
        return view('admin.user.view_notifications', $this->admin_data);

    }
}
