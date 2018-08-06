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
                              action="{{route('admin.user.bank_account.query',['token'=>$token])}}"
                              method="POST" id="post_method">
                            {!! csrf_field() !!}
                            <div class="box-body">
                                <br/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Year</span>
                                            <select type="text" class="form-control" name="year" required>
                                                <option value="">--Select Year--</option>
                                                @for($i=2017;$i<=2075;$i++)
                                                    <option>{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Month</span>
                                            <select type="text" class="form-control" name="month" >
                                                <option value="">--Select Month--</option>
                                                @for($i=1;$i<=12;$i++)
                                                    <option>{{$i}}</option>
                                                @endfor
                                            </select>
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
