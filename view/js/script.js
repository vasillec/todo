function HideErrorBlock(){
  $("#hide-layout, #errorBlock").fadeOut(200);
}

function ShowErrorBlock(mes){
  $("#errorBlock").children('.error-message').text(mes);
  $("#hide-layout, #errorBlock").fadeIn(200);
}

$(function() {
  $(".exit").click(function() {
    HideErrorBlock(); 
  })
  $('#hide-layout').click(function() {
    HideErrorBlock();
  })
})

$(function () {
  $("#auth-fotm").submit(function(event){
    event.preventDefault();
    if($(this).valid()){
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
            ShowErrorBlock(msg);
          }
          else
          {
            window.location.replace("index.php");
          }
        }
      });
    }
    else
    {
     var errors = authValidator.numberOfInvalids();
     if (errors) {
      var message = (errors == 1) ? '1 invalid field' : errors + ' invalid fields';
      ShowErrorBlock(" Your form contains "
        + message + ", see details below.");
    }
  }
});

  $("#reg-form").submit(function(event){
    event.preventDefault();
    if($(this).valid()){
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
            ShowErrorBlock(msg);
          }
          else
          {
            window.location.replace("index.php");
          }
        }
      });
    }
    else{
      var errors = regValidator.numberOfInvalids();
      if (errors) {
        var message = (errors == 1) ? '1 invalid field' : errors + ' invalid fields';
        ShowErrorBlock(" Your form contains "
          + message + ", see details below.");
      }
    }
  });
});

var authValidator;
$(function () {
  authValidator = $("#auth-fotm").validate({
    highlight: function(element) {
      $(element).siblings('div').removeClass('success').addClass('invalid');
    },
    unhighlight: function(element) {
      $(element).siblings('div').removeClass('invalid');
    },
    errorPlacement: function (error, element) {
      error.insertAfter(element);
      error.addClass('invalid');
    },
    onkeyup: false,
    onfocusout: function(element) { $(element).valid(); },
    errorElement: "div",
    rules: {
      login: {
        required: true,
      },
      password: {
        required: true,
      }
    },
    messages: {
      login: {
        required: "✖ This field is required"
      },
      password: {
        required: "✖ This field is required",
      }
    }
  });
});

var regValidator;
$(function () {
  regValidator = $("#reg-form").validate({
    highlight: function(element) {
      $(element).siblings('div').removeClass('success').addClass('invalid');
    },
    unhighlight: function(element) {
      $(element).siblings('div').removeClass('invalid').addClass('success');
    },
    success: function(label) {
      label.addClass("success").text("✔ Ok")
    },
    errorPlacement: function (error, element) {
      error.insertAfter(element);
      error.addClass('invalid');
    },
    onkeyup: false,
    onfocusout: function(element) { $(element).valid(); },
    errorElement: "div",
    rules: {
      login: {
        required: true,
        remote: {
          url: "controller/base.php",
          type: 'POST',
          data: {
            method: "check_login",
            login: function(){ return $('#login').val()}
          }
        }
      },
      password: {
        required: true,
      },
      password2: {
        required: true,
        equalTo: "#password"
      }
    },
    messages: {
      login: {
        required: "✖ This field is required",
        remote: "✖ This name is already in use"
      },
      password: {
        required: "✖ This field is required",
      },
      password2: {
        required: "✖ This field is required",
        equalTo: "✖ Please enter the same value again"
      }
    }
  });
});

