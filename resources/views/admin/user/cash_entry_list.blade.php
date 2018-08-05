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
                @include('flash.message');
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Cash Entry Lists</h3>
                        </div>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>SNo</th>
                                <th>Date</th>
                                <th>Particular</th>
                                <th>Debit Amount</th>
                                <th>Credit Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; ?>
                            @foreach($cash_entry_list as $c)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$c->date}}</td>
                                    <td>{{$c->particular}} </td>
                                    <td>{{$c->debit_amount}} </td>
                                    <td>{{$c->credit_amount}} </td>

                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle" type="button"
                                                    data-toggle="dropdown">Action
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{route('admin.user.cash_entry.list.edit',['id'=>$c->id, 'token'=>$token])}}"
                                                       data-id="{{$c->id}}" class="btn-edit">Edit</a>
                                                </li>
                                                <li><a href="#" data-id="{{$c->id}}"
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

        </section>
    </div>
    <div id="myModal3" class="modal custom fade">
        <div class="modal-dialog" role="document">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Information</h5>
                </div>
                <form id="delete_cash_record">
                    {!! csrf_field() !!}
                    <div class="modal-body">
                        <input name="record_id" type="hidden"/>

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
//        delete
        $('#example1').on('click', '.btn-delete', function (e) {
            var delete_id = $(this).attr('data-id');
            $('#myModal3').modal('show');
            $('[name="record_id"]').val(delete_id);

        });
        $('#delete_cash_record').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('admin.user.cash_entry.list.delete')}}",
                method: "GET",
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
