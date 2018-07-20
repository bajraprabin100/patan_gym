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
                <div class="api_error_message"></div>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Branch Parameter</h3>
                        @include('flash.message')
                        @if(Auth::user()->hasRole('admin') )
                        <span class="pull-right"><button type="button" class="btn btn-info btn-lg" data-toggle="modal"
                                                         data-target="#myModal">Create Branch Parameter</button></span>

                       @endif
                    </div>
                        @if(Entrust::hasRole('admin') )
                    <div class="row">
                        <div class="container">
                            <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{route('admin.branch_para.import',['token'=>$token])}}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                <input type="file" name="import_file" />
                                <button class="btn btn-primary">Import File</button>
                            </form>
                        </div>
                    </div>
                    @endif
                    <!-- /.box-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>SNo</th>
                                        <th>Branch Code</th>
                                        <th>Branch Name</th>
                                        <th>Group Code</th>
                                        <th>Group Name</th>
                                        <th>Receiving branch code</th>
                                        <th>Receiving branch Name</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($branch_paras as $branch_para)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$branch_para->branch_code}}</td>
                                            <td>{{$branch_para->branch_name}}</td>
                                            <td>{{$branch_para->group_code}}</td>
                                            <td>{{$branch_para->group_name}}</td>
                                            <td>{{$branch_para->receiving_branch_name}}</td>
                                            <td>{{$branch_para->receiving_branch_code}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-default dropdown-toggle" type="button"
                                                            data-toggle="dropdown">Action
                                                        <span class="caret"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" data-id="{{$branch_para->id}}" class="btn-edit">Edit</a>
                                                        </li>
                                                        <li><a href="#" data-id="{{$branch_para->id}}"
                                                               class="btn-delete">Delete</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </section>
    </div>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form id="branch_para_submit" class="form-horizontal" role="form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Branch Para</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{ csrf_field() }}
                                <fieldset>


                                    <!-- Text input-->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Branch Code</label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Branch Code" class="form-control"
                                                   name="branch_code" required>
                                        </div>
                                    </div>

                                    <!-- Text input-->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Branch Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" placeholder="Branch Name" class="form-control"
                                                   name="branch_name" required>
                                        </div>
                                    </div>

                                    <!-- Text input-->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Group</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Group Code" class="form-control"
                                                   name="group_code" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Group name" class="form-control"
                                                   name="group_name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Delivery Group Code</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Delivery Group Code" class="form-control"
                                                   name="delivery_group_code" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Delivery Group name" class="form-control"
                                                   name="delivery_group_name" required>
                                        </div>
                                    </div>

                                    <!-- Text input-->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Receiving Branch</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Receiving Branch Code" class="form-control"
                                                   name="receiving_branch_code" required>
                                        </div>

                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Receiving Branch Name" class="form-control"
                                                   name="receiving_branch_name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Branch Company
                                            Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" placeholder="Branch Company Name" class="form-control"
                                                   name="branch_company_name" >
                                        </div>


                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">COD Applicable</label>
                                        <div class="col-sm-4">
                                            <input type="radio" name="cod" value="Yes" checked>Yes<br>
                                            <input type="radio" name="cod" value="No"> No<br>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Vat Applicable</label>
                                        <div class="col-sm-4">
                                            <input type="radio" name="vat_applicable" value="Yes" checked>Yes<br>
                                            <input type="radio" name="vat_applicable" value="No"> No<br>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Address</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Address" class="form-control" name="address"
                                                   >
                                        </div>

                                        {{--<div class="col-sm-6">--}}
                                        {{--<input type="text" placeholder="Email" class="form-control" name="vat_no" required>--}}
                                        {{--</div>--}}
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Vat No</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Vat No" class="form-control" name="vat_no"
                                                   >
                                        </div>

                                        <label class="col-sm-2 control-label" for="textinput">Email</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Email" class="form-control" name="email"
                                                   >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Branch Incharge name</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Branch Incharge Name" class="form-control" name="branch_incharge_name"
                                                   >
                                        </div>

                                        <label class="col-sm-2 control-label" for="textinput">Mobile NO</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Mobile No" class="form-control" name="mobile_no"
                                                  >
                                        </div>
                                    </div>


                                    <!-- Text input-->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Phone</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Phone" class="form-control" name="phone"
                                                   >
                                        </div>
                                        <label class="col-sm-2 control-label" for="textinput">Fax</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Fax" class="form-control" name="fax"
                                                   >
                                        </div>
                                    </div>


                                    <!-- Text input-->

                                </fieldset>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default submit_click">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="edit-branch"></div>

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
                <form action="#" method="Post" id="delete_company">
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
            console.log(base_url);
            $('#example1').on('click', '.btn-edit', function () {
                $.ajax({
                    url: base_url + '/admin/branch_para/' + $(this).attr('data-id') + '/edit',
                    method: "GET",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data: $(this).serialize(),
                    success: function (response) {
                        console.log(response);
                        $('.edit-branch').html(response.data.branch_para_html);
                        $('#myModal2').modal();
                        // location.reload();
                        // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


                    }
                })
            });
            $('#branch_para_submit').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{route('admin.branch_para.store')}}",
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
            $('#branch_para_submit').keyup(function(event){

                if (event.keyCode === 112) {
                    $('.submit_click').click();

                }
            });
            $('#example1').on('click', '.btn-delete', function (e) {
                var delete_id = $(this).attr('data-id');
                $('#myModal3').modal('show');
                $('[name="info_id"]').val(delete_id);

            });
            $('#delete_company').on('submit',function (e) {
                e.preventDefault();
                $.ajax({
                    url: base_url + '/admin/branch_para/' + $('[name=info_id]').val() + '/destroy',
                    method: "DELETE",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");
                    },
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        // location.reload();
                        // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');

                    }
                })

            });


            {{--$('#example1').on('click', '.btn-delete', function (e) {--}}
                {{--e.preventDefault();--}}
                {{--$.ajax({--}}
                    {{--url: base_url + '/admin/branch_para/' + $(this).attr('data-id'),--}}
                    {{--method: "DELETE",--}}
                    {{--beforeSend: function (xhr) {--}}
                        {{--xhr.setRequestHeader('Authorization', 'Bearer ' + "{{$token}}");--}}
                    {{--},--}}
                    {{--data: {--}}
                        {{--"_token": "{{ csrf_token() }}"--}}
                    {{--},--}}
                    {{--success: function (response) {--}}
                        {{--location.reload();--}}
                        {{--// $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');--}}


                    {{--}--}}
                {{--})--}}
            {{--});--}}
        })
    </script>
@endsection
