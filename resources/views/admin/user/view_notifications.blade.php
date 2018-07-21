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

                <div class="col-md-9">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Notifications</h3>


                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">

                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover">
                                    <tbody>
                                    @foreach($all_not as $a)

                                    <tr style={{$a->status==0?"background-color:#ddd":""}}>
                                        <td class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
                                        <td class="mailbox-subject">{{$a->message}}
                                        </td>
                                        <td class="mailbox-attachment"></td>
                                        {{--<td class="mailbox-date">5 mins ago</td>--}}
                                    </tr>
                                    @endforeach
                                    {{--<tr>--}}
                                        {{--<td class="mailbox-star"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>--}}
                                        {{--<td class="mailbox-subject"><b>AdminLTE 2.0 Issue</b> - Trying to find a solution to this problem...--}}
                                        {{--</td>--}}
                                        {{--<td class="mailbox-attachment"><i class="fa fa-paperclip"></i></td>--}}
                                        {{--<td class="mailbox-date">28 mins ago</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td class="mailbox-star"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>--}}
                                        {{--<td class="mailbox-subject"><b>AdminLTE 2.0 Issue</b> - Trying to find a solution to this problem...--}}
                                        {{--</td>--}}
                                        {{--<td class="mailbox-attachment"><i class="fa fa-paperclip"></i></td>--}}
                                        {{--<td class="mailbox-date">11 hours ago</td>--}}
                                    {{--</tr>--}}


                                    </tbody>
                                </table>
                                <!-- /.table -->
                            </div>
                        </div>
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



@endsection
