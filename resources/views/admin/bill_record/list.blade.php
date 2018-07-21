@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboard
                <small>Control panel</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">

                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Bill Record Lists</h3>
                        </div>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>SNo</th>
                                <th>Membership No</th>
                                <th>Bill No</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Paid amount</th>
                                <th>Due Amount</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; ?>
                            @foreach($bill_records as $b)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$b->membership_no}} </td>
                                    <td>{{$b->bill_no}} </td>
                                    <td>{{$b->amount}}</td>
                                    <td>{{$b->discount}}</td>
                                    <td>{{$b->paid_amount}}</td>
                                    <td>{{$b->due_amount}}</td>
                                    <td>{{$b->remarks}}</td>
                                    {{--<td>--}}
                                        {{--<div class="dropdown">--}}
                                            {{--<button class="btn btn-default dropdown-toggle" type="button"--}}
                                                    {{--data-toggle="dropdown">Action--}}
                                                {{--<span class="caret"></span></button>--}}
                                            {{--<ul class="dropdown-menu">--}}
                                                {{--<li><a href="#" data-id="{{$b->id}}" class="btn-edit">Edit</a>--}}
                                                {{--</li>--}}
                                                {{--<li><a href="#" data-id="{{$b->id}}"--}}
                                                       {{--class="btn-delete">Delete</a></li>--}}
                                            {{--</ul>--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                </tr>
                            @endforeach
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!--/.col (left) -->
                <!-- right column -->

                <!--/.col (right) -->
            </div>

        </section>
    </div>

@endsection
