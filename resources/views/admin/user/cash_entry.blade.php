@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Cash Entry
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
                            <h3 class="box-title">Add Debit</h3>
                        </div>

                        <form role="form" id="update_user">
                            {!! csrf_field() !!}
                            <div class="box-body">

                                <br/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Date</span>
                                            <input type="text" class="form-control" placeholder="Date" id="datepicker"
                                                   name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Particular</span>
                                            <input type="text" class="form-control" placeholder="Particular"
                                                   name="particular" required>
                                        </div>
                                    </div>
                                    <br/>
                                    <br/>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Debit</span>
                                            <input type="text" class="form-control" placeholder="Debit"
                                                   name="debit_amount" value="0">
                                        </div>
                                    </div>
                                    {{--<div class="col-md-6">--}}
                                        {{--<div class="input-group">--}}
                                            {{--<span class="input-group-addon">Credit</span>--}}
                                            {{--<input type="text" class="form-control" placeholder="Credit Amount"--}}
                                                   {{--name="credit_amount" value="0">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                </div>
                                <br>
                                <br>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add Credit</h3>
                        </div>

                        <form role="form" id="credit_save">
                            {!! csrf_field() !!}
                            <div class="box-body">

                                <br/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Date</span>
                                            <input type="text" class="datepicker form-control" placeholder="Date" id="datepicker_credit"
                                                   name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Particular</span>
                                            <input type="text" class="form-control" placeholder="Particular"
                                                   name="particular" required>
                                        </div>
                                    </div>
                                    <br/>
                                    <br/>
                                    {{--<div class="col-md-6">--}}
                                        {{--<div class="input-group">--}}
                                            {{--<span class="input-group-addon">Debit</span>--}}
                                            {{--<input type="text" class="form-control" placeholder="Debit"--}}
                                                   {{--name="debit_amount" value="0">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Credit</span>
                                            <input type="text" class="form-control" placeholder="Credit Amount"
                                                   name="credit_amount" value="0">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $('#update_user').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('admin.user.cash_entry.store')}}",
                method: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                },
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success == true) {

                        location.reload();
                    }
                    // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');

                }
            })

        });
        $('#credit_save').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('admin.user.cash_entry.store')}}",
                method: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                },
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success == true) {

                        location.reload();
                    }
                    // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');

                }
            })

        });
    </script>

@endsection