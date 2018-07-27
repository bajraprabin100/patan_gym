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
                                        <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">
                                                     <button type="button" class="btn btn-flat pwd_click">
                                                        Date
                                                    </button>
                                                 </span>
                                            <input type="text" id="datepicker2" class="form-control"
                                                   placeholder="Date" value="{{$bill_record->date}}" name="date" required>
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
                                                   value="{{$bill_record->bill_no}}" placeholder="Bill number" name="bill_no" required readonly>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <label for="">Membership Name: </label>

                                                <a href="{{route('admin.user.bill_detail',['membership_no'=>$bill_record->membership_no,'token'=>$token])}}" data-id="{{$bill_record->membership_no}}"
                                                   class="btn-delete">{{$bill_record->name}}</a>

                                            </select>
                                        </div>
                                    </div>
                                    {{--<div class="col-md-4">--}}
                                    {{--<div class="input-group input-group-sm">--}}
                                    {{--<span class="input-group-btn">--}}
                                    {{--<button type="button" class="btn btn-flat pwd_click">--}}
                                    {{--Member Name--}}
                                    {{--</button>--}}
                                    {{--</span>--}}
                                    {{--<input type="text" class="form-control"--}}
                                    {{--placeholder="Member Name" name="member_name" required >--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Package</span>
                                            <select name="package" class="form-control" required>
                                                <option value="">--Select Package--</option>
                                                @for($i=1;$i<27;$i++)

                                                    <option value="{{$i}}" required {{$i==$bill_record->package?'selected':''}}>{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                <br/>
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Total Amount</span>
                                            <input type="text" class="form-control" placeholder="Amount"
                                                   value="{{$bill_record->amount}}" name="amount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Paid Amount</span>
                                            <input type="text" class="form-control"placeholder="Paid Amount"
                                                   value="{{$bill_record->paid_amount}}" name="paid_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Discount</span>
                                            <input type="text"  class="form-control" placeholder="Discount amt"
                                                   value="{{$bill_record->discount}}"  name="discount">
                                        </div>
                                    </div>


                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Due Amount</span>
                                            <input type="text" class="form-control" placeholder="Due Amount"
                                                   value="{{$bill_record->due_amount}}" name="due_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Remarks</span>
                                            <input type="text" class="form-control" placeholder="Remarks"
                                                   value="{{$bill_record->remarks}}"  name="remarks">
                                        </div>
                                    </div>
                                </div>
                                <br>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
                  location.reload();
                  // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');

              }
          })

      });
      $('[name=package]').click(function (e) {
          e.preventDefault();
          $.ajax({
              url: "{{route('admin.user.package.selected')}}",
              method: "GET",
              beforeSend: function (xhr) {
                  xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
              },
              data: {
                  month: $(this).find('option:selected').val(),
                  admission_date: $("[name=date]").val()
              },
              success: function (response) {
                  if (response.success == false) {
                      $('[name=amount]').val("");
                      $('[name=user_valid_date]').val("");

                  }
                  console.log(response);
                  $('[name=amount]').val(response.data.price);
                  $('[name=user_valid_date]').val(response.data.user_valid_date);
                  // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


              }
          })
          $('[name=amount],[name=discount],[name=paid_amount]').keyup(function () {

              var package_rate =  $('[name=amount]').val();
              var discount = $('[name=discount]').val();
              var paid_amt = $('[name=paid_amount]').val();
              var due_amt= package_rate-discount-paid_amt;
              $('[name=due_amount]').val(due_amt);

          })
      });
  </script>

@endsection