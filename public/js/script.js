function HideErrorBlock() {
    $("#hide-layout, #errorBlock").fadeOut(200);
}

function ShowErrorBlock(mes) {
    $("#errorBlock").children('.error-message').text(mes);
    $("#hide-layout, #errorBlock").fadeIn(200);
}

$(function () {
    $(".exit").click(function () {
        HideErrorBlock();
    })
    $('#hide-layout').click(function () {
        HideErrorBlock();
    })
})

$(function () {
    $("#auth-fotm").submit(function (event) {
        event.preventDefault();
        if ($(this).valid()) {
            $.ajax({
                type: "POST",
                url: "user/auth",
                data: "login=" + $("#login").val() +
                "&pwd=" + $("#password").val(),
                success: function (msg) {
                    if (msg) {
                        try {
                            var res = JSON.parse(msg);
                            if (res.error) {
                                ShowErrorBlock(res.error);
                            }
                            else if(res) {
                                window.location.replace("http://" + document.domain +"/main");
                            }
                        } catch (e) {
                            ShowErrorBlock(msg);
                        }
                    }
                    else {
                        ShowErrorBlock('SERVER ERROR');
                    }
                },
                error: function (msg) {
                    ShowErrorBlock(msg.responseText);
                }
            });
        }
        else {
            var errors = authValidator.numberOfInvalids();
            if (errors) {
                var message = (errors == 1) ? '1 invalid field' : errors + ' invalid fields';
                ShowErrorBlock(" Your form contains "
                    + message + ".");
            }
        }
});

    $("#reg-form").submit(function (event) {
        event.preventDefault();
        if ($(this).valid()) {
            $.ajax({
                type: "POST",
                url: "user/reg",
                data: "login=" + $("#login").val() +
                "&pwd=" + $("#password").val() +
                "&pwd_c=" + $("#password2").val(),
                success: function (response) {
                    response = JSON.parse(response);
                    if (!response.result) {
                        ShowErrorBlock(response.error);
                    }
                    else {
                        window.location.replace("/");
                    }
                },
                success: function (msg) {
                    if (msg) {
                        try {
                            var res = JSON.parse(msg);
                            if (res.error) {
                                ShowErrorBlock(res.error);
                            }
                            else if(res) {
                                window.location.replace("http://" + document.domain + "/main");
                            }
                        } catch (e) {
                            ShowErrorBlock(msg);
                        }
                    }
                    else {
                        ShowErrorBlock('SERVER ERROR');
                    }
                },
                error: function (msg) {
                    ShowErrorBlock(msg.responseText);
                }
            });
        }
        else {
            var errors = regValidator.numberOfInvalids();
            if (errors) {
                var message = (errors == 1) ? '1 invalid field.' : errors + ' invalid fields.';
                ShowErrorBlock(" Your form contains "
                    + message);
            }
        }
    });
});

jQuery.validator.addMethod("pasValid", function(value, element)
{
    return this.optional(element) || /^[A-Za-z0-9]+$/i.test(value);
}, "✖ Letters and numbers only please");

var authValidator;
$(function () {
    authValidator = $("#auth-fotm").validate({
        highlight: function (element) {
            $(element).siblings('div').removeClass('success').addClass('invalid');
        },
        unhighlight: function (element) {
            $(element).siblings('div').removeClass('invalid');
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
            error.addClass('invalid');
        },
        onkeyup: false,
        onfocusout: function (element) {
            $(element).valid();
        },
        errorElement: "div",
        rules: {
            login: {
                required: {
                    depends: function () {
                        $(this).val($(this).val().replace(/[^A-Za-zА-Яа-я0-9-_]/g,''));
                        return true;
                    }
                },
                minlength: 3,
                maxlength: 20,
            },
            password: {
                required: {
                    depends: function () {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                pasValid: true,
                minlength: 5,
                maxlength: 20,
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
        highlight: function (element) {
            $(element).siblings('div').removeClass('success').addClass('invalid');
        },
        unhighlight: function (element) {
            $(element).siblings('div').removeClass('invalid').addClass('success');
        },
        success: function (label) {
            label.addClass("success").text("✔ Ok")
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
            error.addClass('invalid');
        },
        onkeyup: false,
        onfocusout: function (element) {
            $(element).valid();
        },
        errorElement: "div",
        rules: {
            login: {
                required: {
                    depends: function () {
                        $(this).val($(this).val().replace(/[^A-Za-zА-Яа-я0-9-_]/g,''));
                        return true;
                    }
                },
                minlength: 3,
                maxlength: 20,
                remote: {
                    url: "user/check-login",
                    type: 'POST',
                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    data: {
                        login: function () {
                            return $('#login').val();
                        }
                    }
                }
            },
            password: {
                required: {
                    depends: function () {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                pasValid: true,
                minlength: 5,
                maxlength: 20,
            },
            password2: {
                required: {
                    depends: function () {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
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

