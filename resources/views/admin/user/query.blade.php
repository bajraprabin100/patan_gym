@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
               Query
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#">Forms</a></li>
                <li class="active">General Elements</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                @include('flash.message')
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add query</h3>
                        </div>
                        <form role="form" target="_blank"
                              action="{{route('admin.user.cash_entry.query',['token'=>$token])}}"
                              method="POST" id="post_method">
                            {!! csrf_field() !!}
                            <div class="box-body">
                                <br/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Date From</span>
                                            <input type="text" class="form-control datepicker" placeholder="Date From"
                                                   name="date_from" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Date To</span>
                                            <input type="text" class="form-control datepicker2" placeholder="Particular"
                                                   name="date_to" required>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <button type="submit" class="btn btn-primary" style="margin-left:10px">Query</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
