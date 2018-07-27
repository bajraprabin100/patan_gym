@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                User Record
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

                        <form role="form"  id="update_user">
                            {!! csrf_field() !!}
                            <div class="box-body">

                                <br/>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Membership No</span>
                                            <input type="text" class="form-control" placeholder="Member No"
                                                   readonly value="{{$user->membership_no}}"
                                                   name="membership_no">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Name</span>
                                            <input type="text" class="form-control" placeholder="Name"
                                                   value="{{$user->name}}" name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Address</span>
                                            <input type="text" class="form-control" placeholder="Address"
                                                   value="{{$user->address}}" name="address">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">User valid date</span>
                                            <input type="text" class="form-control" placeholder="User valid date"
                                                   value="{{$user->user_valid_date}}" name="user_valid_date">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Gender</span>

                                            <select name="gender" class="form-control">
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Admission Date</span>
                                            <input type="text" class="form-control" placeholder="Admission Date"
                                                   value="{{$user->admission_date}}" name="admission_date">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Package Rate</span>
                                            <input type="text" class="form-control" placeholder="Package Rate"
                                                   value="{{$user->package_rate}}" name="package_rate">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Email</span>
                                            <input type="text" class="form-control" placeholder="Email"
                                                   value="{{$user->email}}" name="email">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Contact</span>
                                            <input type="text" class="form-control" placeholder="Contact"
                                                   value="{{$user->contact}}" name="contact">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">User photo</span>
                                            {{--<input type="file" name="image">--}}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">User status</span>
                                            <input type="text" class="form-control" placeholder="User status"
                                                   value="{{$user->user_status}}" name="user_status">
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
        $('#update_user').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('admin.user.updateDetail')}}",
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
    </script>

@endsection