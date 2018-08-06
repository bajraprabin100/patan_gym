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
                            <h3 class="box-title">Add Bill Record</h3>
                        </div>
                        <div class="box-body">
                            <br/>
                            <form role="form" id="bill_record_submit">
                                {!! csrf_field() !!}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">
                                                     <button type="button" class="btn btn-flat pwd_click">
                                                        Date
                                                    </button>
                                                 </span>
                                            <input type="text" id="datepicker" class="form-control"
                                                   placeholder="Date" name="date" required>
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
                                                   placeholder="Bill number" name="bill_no" required
                                                   readonly value="{{$bill_no}}">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Membership No</span>
                                            <select name="membership_no" class="form-control dest_select_2">
                                                <option value="">Select Membership</option>
                                                <option value="">No member</option>
                                                @foreach($members as $m)
                                                    <option value="{{$m->membership_no}}"
                                                            required>{{$m->membership_no}}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                    <span class="input-group-btn">
                                    <button type="button" class="btn btn-flat pwd_click">
                                    Member Name
                                    </button>
                                    </span>
                                            <input type="text" class="form-control"
                                                   placeholder="Member Name" name="member_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Package</span>
                                            <select name="package" class="form-control" required>
                                                <option value="">--Select Package--</option>
                                                @for($i=1;$i<27;$i++)

                                                    <option value="{{$i}}" required>{{$i}}</option>
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
                                            <input type="text" value="0" class="form-control" placeholder="Amount"
                                                   name="amount">
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
                                            <span class="input-group-addon">Discount</span>
                                            <input type="text" value="0" class="form-control" placeholder="Discount amt"
                                                   name="discount">
                                        </div>
                                    </div>


                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Due Amount</span>
                                            <input type="text" class="form-control" value="0" placeholder="Due Amount"
                                                   name="due_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Remarks</span>
                                            <input type="text" class="form-control" placeholder="Remarks"
                                                   name="remarks">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Valid Date</span>
                                            <input type="text" class="form-control" placeholder="Valid Date"
                                                   name="user_valid_date">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Add Bill</button>
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


            $('[name=package]').keyup(function (e) {
                e.preventDefault();
                packageSelected($(this).find('option:selected').val());

            });
            $('[name=package]').change(function (e) {
                e.preventDefault();
                packageSelected($(this).find('option:selected').val());

            });


            function packageSelected(package) {
                $.ajax({
                    url: "{{route('admin.user.package.selected')}}",
                    method: "GET",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data: {
                        month: package,
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
                });
            }


                $('[name=amount],[name=discount],[name=paid_amount]').keyup(function () {

                    var package_rate = $('[name=amount]').val();
                    var discount = $('[name=discount]').val();
                    var paid_amt = $('[name=paid_amount]').val();
                    var due_amt = package_rate - discount - paid_amt;
                    $('[name=due_amount]').val(due_amt);

                })



        $('.dest_select_2').on('select2:select', function (e) {
            console.log('test');
            var data = e.params.data;
            e.preventDefault();
            $.ajax({
                url: "{{route('admin.user.member.selected')}}",
                method: "GET",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                },
                data: {
                    membership_no: $(this).val()

                },
                success: function (response) {
                    if (response.success == true) {
                        $('[name=member_name]').val(response.data.name);

                    }
                    // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


                }
            })
        });

        })
        ;
    </script>
@endsection
