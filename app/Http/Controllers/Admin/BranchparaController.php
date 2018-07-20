<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BranchPara;
use Session;
use App\Repositories\Backend\BranchPara\BranchParaInterface;
use Input;
use Excel;

class BranchparaController extends DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $admin_data;
    public $branch_para;
    public function __construct(BranchParaInterface $branch_para)
    {
        parent::__construct();
        $this->branch_para=$branch_para;

    }

    public function index()
    {
        $this->admin_data['branch_paras']=$this->branch_para->all();
        return view('admin.branch_para.index',$this->admin_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fillable=[
            'branch_code','branch_name','group_code','group_name',
            'address','vat_no','phone','email','fax',
            'branch_company_name','receiving_branch_name','receiving_branch_code','delivery_group_code','delivery_group_name','cod','vat_applicable',
            'branch_incharge_name','mobile_no'
        ];
        foreach($fillable as $f){
            $data[$f]=($request[$f]!=null)?$request[$f]:'';
        }
        $this->branch_para->create($data);
        Session::flash('successMsg','Branch Information saved successfully');
        return response()->json(['success'=>true,'message'=>'Branch Parameter saved successfully'],200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $branch_para=$this->branch_para->find($id);
        $branch_para_html=view('admin.branch_para.ajaxEdit',compact('branch_para'))->render();
        return response()->json(['success'=>true,'message'=>'Selected function type','data'=>['branch_para_html'=>$branch_para_html]],200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd($id);
    }
    public function update_branch(Request $request,$id){
        $fillable=[
            'branch_code','branch_name','group_code','group_name',
            'address','vat_no','phone','email','fax',
            'branch_company_name','receiving_branch_name','receiving_branch_code','delivery_group_code','delivery_group_name','cod','vat_applicable',
            'branch_incharge_name','mobile_no'
        ];
        foreach($fillable as $f){
            $data[$f]=($request[$f]!=null)?$request[$f]:'';
        }
        $this->branch_para->update($data,$id);
        Session::flash('successMsg','Branch Parameter updated successfully');
        return response()->json(['success'=>true,'message'=>'Branch Parameter updated successfully'],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        $branch=$this->branch_para->delete($id);
        $branch = BranchPara::find($id);
        $branch->delete();
        Session::flash('successMsg','Branch Parameter deleted successfully');
        return response()->json(['success'=>true,'message'=>'Branch Parameter deleted successfully'],200);
    }
    public function import(Request $request){
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();
            importCsv($data,'branch_paras');
        }
        Session::flash('successMsg','Successfully Imported');
        return back();

    }
}
