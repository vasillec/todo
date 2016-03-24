$(document).ready(function(){
    $("#login_btn").click(function(){
        $.ajax({
            type: "POST",
            url: "controller/base.php",
            data: "login=" + $("#login").val() + 
                  "&pwd=" + $("#password").val() +
                  "&method=login",
            success:function(msg)
            {
                if(msg)
                {
                    window.location.replace("index.php");
                }
                else
                {
                    alert("ERROR");
                }
            }
        });
    });

    $("#reg_btn").click(function(){
        var str = $("#login").val();
        $.ajax({
            type: "POST",
            url: "controller/base.php",
            data: "login=" + $("#login").val() +
                  "&pwd=" + $("#password").val() +
                  "&pwd_c=" +$("#password2").val() +
                  "&method=registration",
            success:function(msg)
            {
                if(msg)
                {
                    window.location.replace("index.php");
                }
                else
                {
                    $('#email').parent().addClass('has-error');
                }
            }
        });
    });
});

/*function validation_auth() {
    if ($('#email').val() != "" && $('#pwd').val() != "") {
        $("#btn_login").removeAttr('disabled');
    }
    else {
        $("#btn_login").attr('disabled', 'disabled');
    }
}
function validation_reg(){
    $('#email').parent().removeClass('has-error');
    if ($('#email').val() != "" && $('#pwd').val() != "" && $('#pwd_c').val() != "") {
        if($('#pwd').val() == $('#pwd_c').val())
        {
            $("#button_reg").removeAttr('disabled');
        }
        else {
            $("#button_reg").attr('disabled', 'disabled');
        }
    }
}*/