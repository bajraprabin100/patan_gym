@if(Session::has('successMsg'))
    {{--<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4> {{ $successMsg }}</div>--}}
    <div class="alert alert-success">{{ Session::get('successMsg') }}</div>
@endif
@if(Session::has('errorMsg'))
    {{--<div class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i>Success!</h4> {{ $successMsg }}</div>--}}
    <div class="alert alert-danger">{{ Session::get('errorMsg') }}</div>
    @endif