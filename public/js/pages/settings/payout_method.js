"use strict";

$(function(){
    $("#method").on('change', function(){
        var method = $(this).find(":selected").val();

        if(method == 'bank') {
            $("#bank").css('display', 'block');
            $("#paypal").css('display', 'none');
        } else {
            $("#bank").css('display', 'none');
            $("#paypal").css('display', 'block');
        }
    })
});
