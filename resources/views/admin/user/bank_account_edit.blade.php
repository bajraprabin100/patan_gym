@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Cash Entry List
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
                            <h3 class="box-title">Edit </h3>
                        </div>

                        <form role="form" id="update_bank_account">
                            {!! csrf_field() !!}
                            <div class="box-body">
                                <br/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Date</span>
                                            <input type="text" class="form-control" placeholder="Date" id="datepicker"
                                                   value="{{$bank_list->date}}" name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Particular</span>
                                            <input type="text" class="form-control" placeholder="Particular"
                                                   value="{{$bank_list->particular}}" name="particular" required>
                                        </div>
                                    </div>
                                    <br/>
                                    <br/>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Debit Amount</span>
                                            <input type="text" class="form-control" placeholder="Amount"
                                                   value="{{$bank_list->debit_amount}}"  name="debit_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">Credit Amount</span>
                                            <input type="text" class="form-control" placeholder="Amount"
                                                   value="{{$bank_list->credit_amount}}"  name="credit_amount" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">

                                            <input name="bank_id" value="{{$bank_list->id}}" type="hidden"/>
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
        $('#update_bank_account').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('admin.user.bank_account.list.update')}}",
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