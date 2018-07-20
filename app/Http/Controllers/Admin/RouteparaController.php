<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\LocationHierarachy;
use App\Models\Admin\RoutePara;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class RouteparaController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        Session::forget('route_locations');
        $query = "SELECT LOCATION_CODE, INITCAP(LOCATION_NAME) LOCATION_NAME FROM LH ORDER BY LOCATION_NAME";
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->get();
        $this->admin_data['route_data'] = RoutePara::select('route_code')->where('route_para.branch_code','=',$this->admin_data['login_user']->branch_code)->groupBy('route_code')->get();
//        join('location_hierarchy as l', 'l.location_code', '=', 'route_para.location_code')
        return view('admin.route_para.index', $this->admin_data);
    }

    public function ajaxAddLocation(Request $request)
    {
        $route_locations = Session::get('route_locations');
        $added_location = LocationHierarachy::where('location_code', '=', $request->location_code)->first();

        $push = 'Y';
        $add = (object)[
            'location_code' => $added_location->location_code,
            'location_name' => $added_location->location_name,
            'pickup_tag' => isset($request->pickup_tag) ? $request->pickup_tag : '',
            'delivery_tag' => isset($request->delivery_tag) ? $request->delivery_tag : ''
        ];
        if (isset($route_locations)) {
            foreach ($route_locations as $g) {
                if ($g->location_code == $request->location_code) {
                    $push = 'N';
                }
            }
        } else {
            $route_locations = new Collection();
        }
        if ($push == 'Y') {
            $route_locations->push($add);
        }
        Session::put('route_locations', $route_locations);
        $route_location_html = view('admin.route_para.ajaxRouteLocation', compact('route_locations'))->render();
        return response()->json(['success' => true, 'message' => 'Location Successfully added', 'data' => ['group_location_html' => $route_location_html]], 200);
    }

    public function ajaxRouteLocationDelete(Request $request)
    {
        $route_locations = Session::get('route_locations');
        foreach ($route_locations as $key => $g) {
            if ($g->location_code == $request->location_code) {
                $route = RoutePara::where('route_code', '=', $request->route_code)
                    ->where('location_code', '=', $request->location_code)
                    ->first();
                if ($route) {
                    $route->delete();
                }
                $route_locations->forget($key);
                break;
            }
        }
        $route_location_html = view('admin.route_para.ajaxRouteLocation', compact('route_locations'))->render();
        return response()->json(['success' => true, 'message' => 'Location Successfully added', 'data' => ['group_location_html' => $route_location_html]], 200);
    }

    public function routeUpdate(Request $request)
    {

        $route_locations = Session::get('route_locations');
        foreach ($route_locations as $g) {
            RoutePara::create(['route_code' => $request->route_code, 'route_name' => $request->route_name, 'remarks' => isset($request->remarks)?$request->remarks:'', 'pickup_tag' => isset($request->chkPickup) ? $request->chkPickup : '', 'delivery_tag' => isset($request->chkDelivery) ? $request->chkDelivery : '', 'location_code' => $g->location_code, 'branch_code' => $this->admin_data['login_user']->branch_code]);
        }
        Session::forget('route_locations');
        Session::flash('successMsg', 'Route Parameter Added successfully');
        return response()->json(['success' => true, 'message' => 'Location Successfully added'], 200);
    }

    public function editRoute($route_code, Request $request)
    {
        Session::forget('route_locations');
        $this->admin_data['route'] = RoutePara::where('route_code', '=', $route_code)->where('branch_code','=',$this->admin_data['login_user']->branch_code)->first();
        $this->admin_data['route_locations'] = RoutePara::where('route_code', '=', $route_code)->where('branch_code','=',$this->admin_data['login_user']->branch_code)->get();
        $this->admin_data['locations'] = LocationHierarachy::orderBy('location_name', 'ASC')->get();
        Session::put('route_locations', $this->admin_data['route_locations']);
        return view('admin.route_para.edit', $this->admin_data);

    }

    public function editUpdate(Request $request)
    {

        $route_locations = Session::get('route_locations');
        foreach ($route_locations as $key => $g) {
            $route = RoutePara::where('route_code', '=', $request->route_code)
                ->where('location_code', '=', $g->location_code)
                ->first();
            if (!$route) {
                RoutePara::create(['route_code' => $request->route_code, 'route_name' => $request->route_name, 'remarks' => isset($request->remarks)?$request->remarks:'', 'pickup_tag' => isset($g->pickup_tag) ? $g->pickup_tag : '', 'delivery_tag' => isset($g->delivery_tag) ? $g->delivery_tag : '', 'location_code' => $g->location_code, 'branch_code' => $this->admin_data['login_user']->branch_code]);
            }
        }
        Session::forget('route_locations');
        Session::flash('successMsg', 'Route Parameter Added successfully');

        return response()->json(['success' => true, 'message' => 'Location Successfully added'], 200);
    }

    public function deleteRoutePara($route_code){

        $route_code = RoutePara::where('route_code','=',$route_code)
            ->where('route_para.branch_code','=',$this->admin_data['login_user']->branch_code)
            ->delete();
        Session::flash('successMsg', 'Route Parameter Deleted successfully');

        return response()->json(['success' => true, 'message' => 'Route Parameter Deleted successfully'], 200);
    }

}
