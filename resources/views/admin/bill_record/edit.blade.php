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
                            <h3 class="box-title">Edit</h3>
                        </div>
                        <form role="form" id="update_bill_record">
                            {!! csrf_field() !!}
                            <div class="box-body">

                                <br/>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Membership No</span>
                                            <input type="text" class="form-control" placeholder="Member No"
                                                   readonly value="{{$bill_record->membership_no}}"
                                                   name="membership_no">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Bill No</span>
                                            <input type="text" class="form-control" placeholder="Bill No"
                                                   readonly value="{{$bill_record->bill_no}}" name="bill_no">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Amount</span>
                                            <input type="text" class="form-control" placeholder="Amount"
                                                   value="{{$bill_record->amount}}" name="amount">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Discount</span>
                                            <input type="text" class="form-control" placeholder="Discount"
                                                   value="{{$bill_record->discount}}" name="discount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Paid Amount</span>
                                            <input type="text" class="form-control" placeholder="Paid Amount"
                                                   value="{{$bill_record->paid_amount}}" name="paid_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Due Amount</span>
                                            <input type="text" class="form-control" placeholder="Due Amount"
                                                   value="{{$bill_record->due_amount}}" name="due_amount">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Remarks</span>
                                            <input type="text" class="form-control" placeholder="Remarks"
                                                   value="{{$bill_record->remarks}}" name="remarks">
                                        </div>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
  <script>
      $('#update_bill_record').on('submit', function (e) {
          e.preventDefault();
          $.ajax({
              url: "{{route('admin.billRecord.update')}}",
              method: "POST",
              beforeSend: function (xhr) {
                  xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
              },
              data: $(this).serialize(),
              success: function (response) {
//                  location.reload();
                  // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');

              }
          })

      });
  </script>

@endsection