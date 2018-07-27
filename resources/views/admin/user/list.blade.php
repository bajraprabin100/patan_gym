@extends('admin.admin_layout.layout')
@section('content')
    <?php $token = Session::get('token'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboard
                <small>Control panel</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
            @include('flash.message')
            <!-- left column -->

                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Member List</h3>
                        </div>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>SNo</th>
                                <th>Membership NO</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Admission Date</th>
                                <th>User Valid Date</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; ?>
                            @foreach($members as $p)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$p->membership_no}}</td>
                                    <td>{{$p->name}}</td>
                                    <td>{{$p->address}}</td>
                                    <td>{{$p->admission_date}}</td>
                                    <td>{{$p->user_valid_date}}</td>
                                    <td>{{$p->contact}}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle" type="button"
                                                    data-toggle="dropdown">Action
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <li><a href="{{route('admin.user.edit',['id'=>$p->membership_no,'token'=>$token])}}" data-id="{{$p->id}}" class="btn-edit">Edit</a>
                                                </li>
                                                {{--<li><a href="#" data-id="{{$p->id}}"--}}
                                                       {{--class="btn-delete">Delete</a></li>--}}
                                                <li><a href="{{route('admin.user.bill_detail',['membership_no'=>$p->membership_no,'token'=>$token])}}" data-id="{{$p->membership_no}}"
                                                       class="btn-delete">View bill Detail</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!--/.col (left) -->
                <!-- right column -->

                <!--/.col (right) -->
            </div>

        </section>
    </div>
    <!-- Modal -->

    <div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="edit-branch">
                    <div class="box box-primary">
                        <form role="form" id="update_package">
                            {!! csrf_field() !!}
                            <div class="box-header with-border">
                                <h3 class="box-title">Edit Package</h3>
                            </div>
                            <div class="box-body">
                                <input name="package_id" type="hidden">
                                <div class="input-group ">
                                    <select name="month_pop" class="form-control">
                                        @for($i=1;$i<27;$i++)
                                            <option value="{{$i}}" required>{{$i}}</option>
                                        @endfor
                                    </select>
                                    <span class="input-group-addon">Month</span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Price</label>
                                    <input type="text" class="form-control" id="exampleInputPassword1"
                                           placeholder="Price"
                                           name="price_pop" required>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="myModal3" class="modal custom fade">
        <div class="modal-dialog" role="document">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Information</h5>
                </div>
                <form action="#" method="Post" id="delete_package">
                    {!! csrf_field() !!}
                    <div class="modal-body">
                        <input name="info_id" type="hidden"/>

                        <p>Are you sure to delete this information?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var base_url = "{{url('/')}}";
            <!--edit-->
            $('#example1').on('click', '.btn-edit', function () {
                $.ajax({
                    url: base_url + '/admin/package/' + $(this).attr('data-id') + '/edit',
                    method: "GET",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#myModal2').modal('show');
                        $('option:selected', 'select[name="month_pop"]').removeAttr('selected');
                        $('[name="month_pop"]').find('option[value=' + response.data.package.month + ']').attr('selected', 'selected');
                        $('[name="price_pop"]').val(response.data.package.price);
                        $('[name="package_id"]').val(response.data.package.id);
                    }
                })
            });
            $('#update_package').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{route('admin.package.update_detail')}}",
                    method: "POST",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },

                    data: $(this).serialize(),
                    success: function (response) {
                        location.reload();
                    }

                })
            });

            $('#branch_para_submit').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{route('admin.package.store')}}",
                    method: "POST",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data: $(this).serialize(),
                    success: function (response) {
                        console.log(response);
                        location.reload();
                        // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


                    }
                })
            });
            $('#branch_para_submit').keyup(function (event) {

                if (event.keyCode === 112) {
                    $('.submit_click').click();

                }
            });

            <!--delete-->
            $('#example1').on('click', '.btn-delete', function (e) {
                var delete_id = $(this).attr('data-id');
                $('#myModal3').modal('show');
                $('[name="info_id"]').val(delete_id);

            });
            $('#delete_package').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: base_url + '/admin/package/' + $('[name=info_id]').val() + '/deletePackage',
                    method: "DELETE",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        location.reload();
                        // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');

                    }
                })

            });


        })
    </script>
@endsection
