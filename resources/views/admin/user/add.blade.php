@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Members
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
                            <h3 class="box-title">Members</h3>
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
                                            <div class="error_msg alert alert-danger alert-dismissible fade in"
                                                 style="display:none"><a href="#" class="close" data-dismiss="alert"
                                                                         aria-label="close">&times;</a> <strong>Danger!</strong>
                                                This alert box could indicate a dangerous or potentially negative action.
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Membership No</span>
                                                    <input type="text" class="form-control" placeholder="Member No"
                                                           name="membership_no" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Bill No</span>
                                                    <input type="text" class="form-control" placeholder="Bill No"
                                                           name="bill_no" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">    <button type="button"
                                                                                           class="btn btn-info btn-flat pwd_click">Name
                                                    </button>
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Name" name="name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Address</span>
                                                    <input type="text" class="form-control" placeholder="Address"
                                                           name="address">
                                                </div>
                                            </div>

                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Contact</span>
                                                    <input type="text" class="form-control" placeholder="Contact"
                                                           name="contact">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Gender</span>
                                                    <select name="gender" class="form-control">
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Others">Others</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">    <button type="button"
                                                                                           class="btn btn-info btn-flat pwd_click">Admission Date
                                                    </button>
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Admission Date" name="admission_date"
                                                           value="{{date('Y-m-d')}}" required>
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
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Package</span>
                                                    <select name="select_package" class="form-control" required>
                                                        <option value="">Select Package</option>
                                                        @foreach($packages as $p)
                                                            <option value="{{$p->month}}">{{$p->month}} month</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                 <span class="input-group-btn">    <button type="button"
                                                                                           class="btn btn-info btn-flat pwd_click">Package Rate
                                                    </button>
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Package Rate" name="package_rate">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Discount</span>
                                                    <input type="text" name="discount" class="form-control" value="0">

                                                </div>
                                            </div>


                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                   <span class="input-group-btn">
                                                       <button type="button"
                                                               class="btn btn-info btn-flat pwd_click">Paid Amount
                                                    </button>
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Paid Amount" name="paid_amount" value="0">
                                                </div>
                                            </div>


                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                 <span class="input-group-addon">
                                                     Due Amount
                                                        </span>

                                                    <input type="text" class="form-control"
                                                           placeholder="Due Amount" name="due_amount">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                        <span class="input-group-addon">
                                                   User Valid Date
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           placeholder="User valid date"
                                                           name="user_valid_date">
                                                </div>
                                            </div>

                                        </div>
                                        {{--<div class="col-md-4">--}}
                                        {{--<div class="input-group">--}}
                                        {{--<span class="input-group-addon">User Valid Date</span>--}}
                                        {{--<input type="text" class="form-control" placeholder="User valid date"--}}
                                        {{--name="email">--}}
                                        {{--</div>--}}
                                        {{--</div>--}}

                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>

    <script>
        $('[name=select_package]').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('admin.user.package.selected')}}",
                method: "GET",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                },
                data: {
                    month: $(this).find('option:selected').val(),
                    admission_date: $("[name=admission_date]").val()
                },
                success: function (response) {
                    if (response.success == false) {
                        $('[name=package_rate]').val("");
                        $('[name=user_valid_date]').val("");

                    }
                    console.log(response);
                    $('[name=package_rate]').val(response.data.price);
                    $('[name=user_valid_date]').val(response.data.user_valid_date);
                    // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


                }
            })
            $('[name=package_rate],[name=discount],[name=paid_amount]').keyup(function () {
                
                var package_rate =  $('[name=package_rate]').val();
                var discount = $('[name=discount]').val();
                var paid_amt = $('[name=paid_amount]').val();
                var due_amt= package_rate-discount-paid_amt;
                $('[name=due_amount]').val(due_amt);

            })
        });
            $('#customer_para').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{route('admin.user.store')}}",
                    method: "POST",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data:$(this).serialize(),
                    success: function (response) {

                      $('[name=bill_no]').val(response.data.bill_no);
                      $('[name=membership_no]').val(response.data.membership_no);
                        $(".error_msg").text(response.message);
                        $(".error_msg").fadeIn(300).delay(1500).fadeOut(400);

                    }
                })
        })
    </script>
@endsection