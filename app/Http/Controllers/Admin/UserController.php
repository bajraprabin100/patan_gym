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
use App\Models\Admin\BillStockPara;
use  App\Repositories\Backend\BranchPara\BranchParaInterface;
use App\Models\Admin\BankAccount;

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
    public function validDate(Request $request){
        $time = strtotime($request->admission_date);
        $user_valid_date = date("Y-m-d", strtotime("+24 month", $time));
        return response()->json(['success' => true, 'message' => '', 'data' => ['user_valid_date' => $user_valid_date]], 200);

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
        $bill = BillStockPara::orderBy('id', 'desc')->first();

        $length = strlen($bill->prefix);
        $query = BillsRecord::select(DB::raw("MAX(CAST(SUBSTRING(bill_no," . $length . "+1) AS UNSIGNED)+1) AS bill_no"))->whereRaw("SUBSTRING(bill_no,1," . $length . ") ='" . $bill->prefix . "'")->first();
        if ($query->bill_no != null) {
            $this->admin_data['bill_no'] = $bill->prefix . $query->bill_no;
        } else {
            $this->admin_data['bill_no'] = $bill->prefix . '1';
        }
        $this->admin_data['members'] = Member::all();
        return view('admin.bill_record.index', $this->admin_data);
    }

    public function storeBillRecord(Request $request)
    {
        $bil_record = new BillsRecord();
        $bil_record->date = isset($request->date) ? $request->date : '0';
        $bil_record->membership_no = isset($request->membership_no) ? $request->membership_no : '';
        $bil_record->name = isset($request->name) ? $request->name : '';
//        $query = BillsRecord::select(DB::raw("MAX(bill_no)+1 AS bill_no"))->first();
//        if ($query->bill_no != null) {
        $bil_record->bill_no = $request->bill_no;
//        } else {
//            $bil_record->bill_no = 1;
//        }
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


//        $member = Member::where('membership_no', '=', $request->membership_no)->first();
//        if ($member) {
//            $member->user_valid_date = $request->user_valid_date;
//            $member->save();
//        }


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
        $bill = BillStockPara::orderBy('id', 'desc')->first();

        $length = strlen($bill->prefix);
        $query = BillsRecord::select(DB::raw("MAX(CAST(SUBSTRING(bill_no," . $length . "+1) AS UNSIGNED)+1) AS bill_no"))->whereRaw("SUBSTRING(bill_no,1," . $length . ") ='" . $bill->prefix . "'")->first();
        if ($query->bill_no != null) {
            $this->admin_data['bill_no'] = $bill->prefix . $query->bill_no;
        } else {
            $this->admin_data['bill_no'] = $bill->prefix . '1';
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
                'membership_no', 'name', 'address', 'user_valid_date', 'gender', 'admission_date', 'package_rate', 'email', 'contact', 'photo', 'user_status', 'is_member'
            ];


            foreach ($input as $i) {

                $data[$i] = isset($data[$i]) ? $data[$i] : '';
            }
            $data['is_member'] = isset($request->is_member) ? $request->is_member : 0;

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

    public function cashEntryList()
    {
        $this->admin_data['cash_entry_list'] = CashBook::all();
        return view('admin.user.cash_entry_list', $this->admin_data);
    }

    public function editCashEntryList($id)
    {
        $this->admin_data['cash_list'] = CashBook::find($id)->first();
        return view('admin.user.cash_list_edit', $this->admin_data);

    }
    public function updateCashEntryList(Request $request){
        $cash_list = CashBook::where('id','=',$request->cash_id)->first();
        $cash_list->date =$request->date;
        $cash_list->particular =$request->particular;
        $cash_list->debit_amount =$request->debit_amount;
        $cash_list->credit_amount =$request->credit_amount;
        $cash_list->save();
        Session::flash('successMsg', 'Cash List updated successfully');
        return response()->json(['success'=>true],200);
    }

    public function deleteCashEntryList(Request $request)
    {
        $cash_list = CashBook::where('id', '=', $request->record_id);
        $cash_list->delete();
        Session::flash('successMsg', 'Record Deleted Successfully');
    }

    public function cashEntryQuery(Request $request)
    {
        $this->admin_data['attribute'] = $request->all();
        $this->admin_data['cash_book'] = CashBook::whereYear('date', '=', $request->year)
            ->where(function ($query) use ($request) {
                if (isset($request->month)) {
                    $query->whereMonth('date', '=', $request->month);
                }
            })
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

    public function bankAccount()
    {
        return view('admin.user.bank_account', $this->admin_data);

    }

    public function bankEntryStore(Request $request)
    {

        $book = new BankAccount();
        $book->date = $request->date;
        $book->particular = isset($request->particular) ? $request->particular : '';
        $book->debit_amount = isset($request->debit_amount) ? $request->debit_amount : 0;
        $book->credit_amount = isset($request->credit_amount) ? $request->credit_amount : 0;
        $book->save();
        Session::flash('successMsg', 'Content saved successfully');
        return response()->json(['success' => true]);
    }
    public function bankAccountList()
    {
        $this->admin_data['bank_account_list'] = BankAccount::all();
        return view('admin.user.bank_account_list', $this->admin_data);
    }
    public function editBankAccountList($id){
        $this->admin_data['bank_list'] = BankAccount::where('id','=',$id)->first();
        return view('admin.user.bank_account_edit', $this->admin_data);
    }
    public function updateBankAccountList(Request $request){
        $bank_account_list = BankAccount::where('id','=',$request->bank_id)->first();
        $bank_account_list->date =$request->date;
        $bank_account_list->particular =$request->particular;
        $bank_account_list->debit_amount =$request->debit_amount;
        $bank_account_list->credit_amount =$request->credit_amount;
        $bank_account_list->save();
        Session::flash('successMsg', 'Bank Account updated successfully');
        return response()->json(['success'=>true],200);
    }
    public function deleteBankAccountList(Request $request){
        $bank_list = BankAccount::where('id', '=', $request->record_id);
        $bank_list->delete();
        Session::flash('successMsg', 'Record Deleted Successfully');

    }

    public function bankAccountQuery(){
        return view('admin.user.bank_account_query', $this->admin_data);

    }
    public function bankEntryQuery(Request $request)
    {
        $this->admin_data['attribute'] = $request->all();
        $this->admin_data['cash_book'] = BankAccount::whereYear('date', '=', $request->year)
            ->where(function ($query) use ($request) {

                if (isset($request->month)) {
                    $query->whereMonth('date', '=', $request->month);
                }
            })
            ->get();
        $debit_amount=DB::table('cash_book')->whereYear('date', '=', $request->year)
        ->where(function ($query) use ($request) {

            if (isset($request->month)) {
                $query->whereMonth('date', '=', $request->month);
            }
        })->sum('debit_amount');
        $credit_amount=DB::table('cash_book')->whereYear('date', '=', $request->year)
        ->where(function ($query) use ($request) {

            if (isset($request->month)) {
                $query->whereMonth('date', '=', $request->month);
            }
        })->sum('credit_amount');
        $this->admin_data['total']=$debit_amount-$credit_amount;
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('admin.user.bank_pdf', $this->admin_data)->render());
        return $pdf->setPaper('A4', 'portrait')->stream();

    }
}
