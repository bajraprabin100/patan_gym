@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Customer
                <small>Setup</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#">Forms</a></li>
                <li class="active">General Elements</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">

                <!-- left column -->
                <div class="col-md-12">
                    @include('flash.message')
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Customer Parameter</h3>
                        </div>

                        <!-- Nav tabs -->


                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="home">

                                <form role="form" id="customer_para">
                                    {!! csrf_field() !!}
                                    <div class="box-body">

                                        <br/>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Membership No</span>
                                                    <input type="text" class="form-control" placeholder="Member No"
                                                           name="memership_no" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">    <button type="button" class="btn btn-info btn-flat pwd_click">Name
                                                    </button>
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Name" name="name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Address</span>
                                                    <input type="text" class="form-control" placeholder="Address"
                                                           name="address">
                                                </div>
                                            </div>

                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Gender</span>
                                                   <select name="gender" class="form-control">
                                                       <option value="Male">Male</option>
                                                       <option value="Female">Female</option>
                                                       <option value="Others">Others</option>
                                                   </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">    <button type="button" class="btn btn-info btn-flat pwd_click">Admission Date
                                                    </button>
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Admission Date" name="admission_date">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Email</span>
                                                    <input type="text" class="form-control" placeholder="Email"
                                                           name="email">
                                                </div>
                                            </div>

                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Package</span>
                                                    <select name="select_package" class="form-control">
                                                        @foreach($packages as $p)
                                                        <option value="{{$p->month}}">{{$p->month}} month</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">    <button type="button" class="btn btn-info btn-flat pwd_click">Package Rate
                                                    </button>
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Package Rate" name="package_rate">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">User Valid Date</span>
                                                    <input type="text" class="form-control" placeholder="User valid date"
                                                           name="email">
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection