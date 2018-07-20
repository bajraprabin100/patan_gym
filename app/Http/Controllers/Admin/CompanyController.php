<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use View;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\Admin\Company;
use Flash;
class CompanyController extends DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function __construct()
   {
       parent::__construct();
   }

    public function index()
    {
        $this->admin_data['company_infos'] = Company::all();
        return view('admin.company.index', $this->admin_data);
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        die(var_dump($request->all()));
        //
        $data=$request->all();
        $data['fax']=isset($request->fax)?$request->fax:'';
        Company::create($data);
//        $request->session()->flash('successMsg', 'success');
        Session::flash('successMsg', 'Company Information saved successfully');

        return response()->json(['success' => true, 'message' => 'Company Information saved successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company_info = Company::find($id);
        $company_info_html = view('admin.company.ajaxEdit', compact('company_info'))->render();
        return response()->json(['success' => true, 'message' => 'Selected function type', 'data' => ['company_info_html' => $company_info_html]], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    public function update_company(Request $request, $id)
    {
        $company = Company::find($id);
        $data=$request->all();
        $data['fax']=isset($request->fax)?$request->fax:'';
        $company->update($data);
        Session::flash('successMsg', 'Company Parameter updated successfully');
        return response()->json(['success' => true, 'message' => 'Company Parameter updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $company = Company::find($id);
        $company->delete();
        Session::flash('successMsg', 'Company Parameter has been deleted successfully');
        return response()->json(['success' => true, 'message' => 'Company Parameter has been deleted successfully'], 200);
    }
}
