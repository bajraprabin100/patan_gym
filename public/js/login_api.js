$(document).ready(function(){
    $('#app_login').submit(function(e){
        e.preventDefault();
        console.log($(this).serialize());
        $.ajax({
            method:"POST",
            url:api_url,
            data:$(this).serialize(),
            success:function(response,status,xhr){
                console.log(xhr);
                if(response.success == false){
                       $('.api_error_message').html('<div class="alert alert-danger alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i> Alert!</h4>'+response.message+' </div>');
                }else{
                    window.location.href=success_url+'?token='+response.data['token'];
                    // window.location.href=success_url+'?token='+response.data['token'];
                    // window.location.href=success_url;
                    // $('.api_error_message').html('<div class="alert alert-danger alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <h4><i class="icon fa fa-ban"></i> Alert!</h4>'+response.message+' </div>');

                }

            }
        });
    });
})