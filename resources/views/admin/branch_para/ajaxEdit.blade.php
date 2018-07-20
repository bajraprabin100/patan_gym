<?php $token=Session::get('token'); ?>
<form id="branch_para_update" class="form-horizontal" role="form" data-id="{{$branch_para->id}}">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Branch Edit</h4>
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
                            <input type="text" placeholder="Branch Code" class="form-control" name="branch_code" required value="{{$branch_para->branch_code}}">
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Branch Name</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="Branch Name" class="form-control" name="branch_name" required value="{{$branch_para->branch_name}}">
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Group</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Group Code" class="form-control" name="group_code" required value="{{$branch_para->group_code}}">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" placeholder="Group name" class="form-control" name="group_name" required value="{{$branch_para->group_name}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Delivery Group Code</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Delivery Group Code" class="form-control"
                                   name="delivery_group_code" required value="{{$branch_para->delivery_group_code}}">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" placeholder="Delivery Group name" class="form-control"
                                   name="delivery_group_name" required value="{{$branch_para->delivery_group_name}}">
                        </div>
                    </div>
                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Receiving Branch</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Receiving Branch Code" class="form-control" name="receiving_branch_code" required value="{{$branch_para->receiving_branch_code}}">
                        </div>

                        <div class="col-sm-6">
                            <input type="text" placeholder="Receiving Branch Name" class="form-control" name="receiving_branch_name" required value="{{$branch_para->receiving_branch_name}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Branch Company Name</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="Branch Company Name" class="form-control" name="branch_company_name"  value="{{$branch_para->branch_company_name}}">
                        </div>


                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">COD Applicable</label>
                        <div class="col-sm-4">
                            <input type="radio" name="cod" value="Yes" {{$branch_para->cod=='Yes'?'checked':''}}>Yes<br>
                            <input type="radio" name="cod" value="No" {{$branch_para->cod=='No'?'checked':''}}> No<br>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Vat Applicable</label>
                        <div class="col-sm-4">
                            <input type="radio" name="vat_applicable" value="Yes" {{$branch_para->vat_applicable=='Yes'?'checked':''}}>Yes<br>
                            <input type="radio" name="vat_applicable" value="No" {{$branch_para->vat_applicable=='No'?'checked':''}}> No<br>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Address</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Address" class="form-control" name="address" value="{{$branch_para->address}}">
                        </div>

                        {{--<div class="col-sm-6">--}}
                        {{--<input type="text" placeholder="Email" class="form-control" name="vat_no" required>--}}
                        {{--</div>--}}
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Vat No</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Vat No" class="form-control" name="vat_no" value="{{$branch_para->vat_no}}">
                        </div>

                        <label class="col-sm-2 control-label" for="textinput">Email</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Email" class="form-control" name="email" value="{{$branch_para->email}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Branch Incharge name</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Branch Incharge Name" class="form-control" name="branch_incharge_name"
                                  value="{{$branch_para->branch_incharge_name}}">
                        </div>

                        <label class="col-sm-2 control-label" for="textinput">Mobile NO</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Mobile NO" class="form-control" name="mobile_no"
                                  value="{{$branch_para->mobile_no}}">
                        </div>
                    </div>


                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Phone</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Phone" class="form-control" name="phone" value="{{$branch_para->phone}}">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Fax</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Fax" class="form-control" name="fax"  value="{{$branch_para->fax}}">
                        </div>
                    </div>



                    <!-- Text input-->

                </fieldset>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-default">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</form>
<script>
    var base_url="{{url('/')}}";
    $('#branch_para_update').submit(function(e){
        e.preventDefault();
        $.ajax({
            url:base_url+'/admin/branch_para/'+$(this).attr('data-id')+'/update_branch',
            type:"POST",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer '+"{{$token}}");
            },
            data:$(this).serialize(),
            success:function(response){
                console.log(response);
                location.reload();
                // $('.api_error_message').html('<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4>'+response.message+' </div>');


            }
        })
    });
</script>
