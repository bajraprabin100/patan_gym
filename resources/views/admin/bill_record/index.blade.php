@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Bill Records
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

                <!-- left column -->
                <div class="col-md-12">
                    @include('flash.message')
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Bill Record</h3>
                        </div>
                        <div class="box-body">
                            <br/>
                            <form role="form" id="bill_record_submit">
                                {!! csrf_field() !!}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Membership No</span>
                                            <select name="membership_no" class="form-control">

                                                @for($i=1;$i<27;$i++)
                                                    <option value="{{$i}}" required>{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">
                                                     <button type="button" class="btn btn-flat pwd_click">
                                                         Bill No.
                                                    </button>
                                                 </span>
                                            <input type="text" class="form-control"
                                                   placeholder="Bill number" name="bill_no" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Amount</span>
                                            <input type="text" value="0" class="form-control" placeholder="Amount"
                                                   name="amount">
                                        </div>
                                    </div>

                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Discount</span>
                                            <input type="text" value="0" class="form-control" placeholder="Discount amt"
                                                   name="discount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Paid Amount</span>
                                            <input type="text" class="form-control" value="0" placeholder="Paid Amount"
                                                   name="paid_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Due Amount</span>
                                            <input type="text" class="form-control" value="0" placeholder="Due Amount"
                                                   name="due_amount">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Remarks</span>
                                            <input type="text" class="form-control" placeholder="Remarks"
                                                   name="remarks">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    </section>
    </div>

    <script>
        $(document).ready(function () {
            var base_url = "{{url('/')}}";

            $('#bill_record_submit').submit(function (e) {
                console.log($(this).serialize());
                e.preventDefault();
                $.ajax({
                    url: "{{route('admin.billRecord.store')}}",
                    method: "POST",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data: $(this).serialize(),
                    success: function (response) {
//                        console.log(response);
                        location.reload();
                        // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


                    }
                })
            });

        })
    </script>
@endsection
