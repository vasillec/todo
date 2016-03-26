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
