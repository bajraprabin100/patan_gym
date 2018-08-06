@extends('admin.admin_layout.layout')
@section('content')
    <link rel="stylesheet" href="{{url('nepali-datepicker/nepaliDatePicker.min.css')}}">
    <script src="{{url('nepali-datepicker/jquery.nepaliDatePicker.min.js')}}"></script>
    <?php $token=Session::get('token'); ?>
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
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
                        <h3 class="box-title">Bill Issue</h3>
                        @include('flash.message')
                        <span class="pull-right"><button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Create Bill Issue</button></span>
                    </div>
                    <!-- /.box-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>SNo</th>
                                        <th>Issue Id</th>
                                        <th>Prefix</th>
                                        <th>Bill No From</th>
                                        <th>Bill No To</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1; ?>
                                    @foreach($bill_issues as $bill_issue)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$bill_issue->issue_id}}</td>
                                            <td>{{$bill_issue->prefix}}</td>
                                            <td>{{$bill_issue->bill_no_from}}</td>
                                            <td>{{$bill_issue->bill_no_to}}</td>
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
                <form id="company_submit" class="form-horizontal" role="form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Bill Issue</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{ csrf_field() }}
                                <fieldset>


                                    <!-- Text input-->

                                    <!-- Text input-->
                                    <div class="form-group">
                                        <span class="bill_error"></span>
                                        <label class="col-sm-2 control-label" for="textinput">Issued Number</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="issue_id"  readonly autofocus>
                                        </div>
                                        <label class="col-sm-2 control-label" for="textinput">Prefix</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Prefix" class="form-control" name="prefix" required>
                                        </div>
                                    </div>



                                    <!-- Text input-->

                                    <!-- Text input-->



                                    <!-- Text input-->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="textinput">Bill Issue No from</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Bill no Issue From" class="form-control bill_no_from" name="bill_no_from" required>
                                        </div>
                                        <label class="col-sm-2 control-label" for="textinput">To</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Bill no Issue To" class="form-control bill_no_to" name="bill_no_to" required>
                                        </div>
                                    </div>
                                        </fieldset>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script src="{{url('js/custom.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('#company_submit').submit(function(e){
                e.preventDefault();
                $.ajax({
                    url:"{{route('admin.utility.bill_issue')}}",
                    method:"POST",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer '+"{{$token}}");
                    },
                    data:$(this).serialize(),
                    success:function(response){
                        console.log(response);
                        if(response.success== false){
                            $('.bill_error').html('<p style="color:red">'+response.message+'</p>');
                        }else
                        $('[name=issue_id]').val(response.data.issue_id);
                        // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


                    }
                })
            });
        })
        var base_url ="{{url('/')}}";
        var currentDate = new Date();
        var currentNepaliDate = calendarFunctions.getBsDateByAdDate(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
        var formatedNepaliDate = calendarFunctions.bsDateFormat("%y-%m-%d", currentNepaliDate.bsYear, currentNepaliDate.bsMonth, currentNepaliDate.bsDate);
        $('.from-book').val(currentNepaliDate.bsYear+'-'+currentNepaliDate.bsMonth+'-'+currentNepaliDate.bsDate);
        var converter = new DateConverter();
        converter.setNepaliDate(currentNepaliDate.bsYear, currentNepaliDate.bsMonth, currentNepaliDate.bsDate)
        // alert(converter.getEnglishYear()+"-"+converter.getEnglishMonth()+"-"+converter.getEnglishDate())
        $('.from-picker-ad').val(converter.getEnglishYear() + "-" + converter.getEnglishMonth() + "-" + converter.getEnglishDate());

        $('.bill_no_from,.bill_no_to').keyup(function(){
            var bill_no_from=$('[name=bill_no_from]').val();
            var bill_no_to=$('[name=bill_no_to]').val();
            var diff=(bill_no_to-bill_no_from) +1;
            $('.differ').val(diff);
        })
        $('.differ').keyup(function(){
            var bill_no_from=$('[name=bill_no_from]').val();
            var add=$(this).val();
            var total=(Number(bill_no_from)+Number(add))-1;
            console.log(total);
            $('[name=bill_no_to]').val(total);
        })
    </script>
@endsection
