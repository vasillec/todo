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
    $.ajax({
        type: "POST",
        url: "http://" + document.domain + "/ajax/get-projects",
        success: function (msg) {
            if (msg) {
                try {
                    var res = JSON.parse(msg);
                    if (res.error) {
                        ShowErrorBlock(res.error);
                        return;
                    }
                    $(".delete_after").remove();
                    res.Status = function () {
                        return (this.status == 1) ? "checked" : "";
                    };
                    $(".projects_container").append(Mustache.render($('#template').html(), res));
                    $('#addTodoList').removeAttr('disabled', 'disabled');
                    Sortable_tasks();
                    Autoresize_texterea();
                }
                catch ($e) {
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
});

function Autoresize_texterea() {
    $('textarea').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow: hidden;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
}

function Sortable_tasks() {
    $(".tasks-list").sortable(
        {
            axis: 'y',
            cursor: 'move',
            handle: '.drag-task',
            opacity: 0.9,
            start: function (event, ui) {
                ui.item.css({
                    background: '#FCFED5'
                })
            },
            stop: function (event, ui) {
                ui.item.css({
                    background: '#fff'
                });
            },
            update: function (event, ui) {
                var order = $(this).sortable('toArray', {attribute: 'data-id'});
                $.ajax({
                    type: "POST",
                    url: "http://" + document.domain + "/ajax/sort-tasks",
                    data: "arr=" + order,

                    success: function (msg) {
                        if (msg) {
                            try {
                                var res = JSON.parse(msg);
                                if (res.error) {
                                    ShowErrorBlock(res.error);
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
        }
    );
}

function Project_edit(elem) {
    var inp = $(elem).closest('.project-header').find('.project_name');
    inp.removeAttr('disabled');
    inp.focus();
    $('#addTodoList').attr('disabled', 'disabled');
    $("input[type=checkbox]").attr('disabled', 'disabled');
    $(".add_task_name").attr('disabled', 'disabled');
    $(".toolbar--task-list").attr("style", "visibility: hidden");
    $(".toolbar--header").attr("style", "visibility: hidden");
}

function Save_add_project(name) {
    if (name.length > 0) {
        $.ajax({
            type: "POST",
            url: "http://" + document.domain + "/ajax/add-project",
            data: "name=" + name,

            success: function (msg) {
                if (msg) {
                    try {
                        var res = JSON.parse(msg);
                        if (res.error) {
                            ShowErrorBlock(res.error);
                            $(".delete_after").remove();
                            $('#addTodoList').removeAttr('disabled', 'disabled');
                            return;
                        }
                        $(".delete_after").remove();
                        res.Status = function () {
                            return (this.status == 1) ? "checked" : "";
                        };
                        $(".projects_container").append(Mustache.render($('#template').html(), res));
                        $('#addTodoList').removeAttr('disabled', 'disabled');
                        Sortable_tasks();
                        Autoresize_texterea();
                    }
                    catch ($e) {
                        ShowErrorBlock(msg);
                    }
                }
                else {
                    ShowErrorBlock('SERVER ERROR');
                    $(".delete_after").remove();
                    $('#addTodoList').removeAttr('disabled', 'disabled');
                }
            },
            error: function (msg) {
                ShowErrorBlock(msg.responseText);
                $(".delete_after").remove();
                $('#addTodoList').removeAttr('disabled', 'disabled');
            }
        });
    }
    else {
        $(".delete_after").remove();
        $('#addTodoList').removeAttr('disabled', 'disabled');
    }
}

function Lost_focus(elem) {
    if ($(elem).val() != "") {
        $(elem).attr('disabled', 'disabled');
        $('#addTodoList').removeAttr('disabled');
        $("input[type=checkbox]").removeAttr('disabled');
        $(".add_task_name").removeAttr('disabled');
        $(".toolbar--task-list").attr("style", "visibility: visible");
        $(".toolbar--header").attr("style", "visibility: visible");
    }
}

function Change_project_name(elem, id) {
    if ($(elem).val() != "") {
        $.ajax({
            type: "POST",
            url: "http://" + document.domain + "/ajax/rename-project",
            data: "id=" + id + "&name=" + $(elem).val(),

            success: function (msg) {
                if (msg) {
                    try {
                        var res = JSON.parse(msg);
                        if (res.error) {
                            ShowErrorBlock(res.error);
                            $(elem).val = 'Project';
                        }
                        $('#addTodoList').removeAttr('disabled', 'disabled');
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
}

function project_delete(id) {
    $.ajax({
        type: "POST",
        url: "http://" + document.domain + "/ajax/delete-project",
        data: "id=" + id,
        success: function (msg) {
            if (msg) {
                try {
                    var res = JSON.parse(msg);
                    if (res.error) {
                        ShowErrorBlock(res.error);
                        return;
                    }
                    res.Status = function () {
                        return (this.status == 1) ? "checked" : "";
                    };
                    $(".projects_container").html(Mustache.render($('#template').html(), res));
                    Sortable_tasks();
                    Autoresize_texterea();
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

function Add_project() {
    $('#addTodoList').attr('disabled', 'disabled');
    var json = {projects: [{"name": ""}]};
    $(".projects_container").append(Mustache.render($('#template_add_project').html(), json));
    $('.project_name_add').focus();
}

function Validation_task(elem) {
    if ($(elem).val() != "") {
        $(elem).parent().find('#add_task_btn').removeAttr('disabled', 'disabled');
    }
    else {
        $(elem).parent().find('#add_task_btn').attr('disabled', 'disabled');
    }
}

function Add_task(elem, id) {
    var name = $(elem).parent('.edit-form--task').children('.add_task_name').val();
    $.ajax({
        type: "POST",
        url: "http://" + document.domain + "/ajax/add-task",
        data: "project_id=" + id + "&name=" + name,

        success: function (msg) {
            if (msg) {
                try {
                    var res = JSON.parse(msg);
                    if (res.error) {
                        ShowErrorBlock(res.error);
                        return;
                    }
                    res.Status = function () {
                        return (this.status == 1) ? "checked" : "";
                    };
                    $(".projects_container").html(Mustache.render($('#template').html(), res));
                    Sortable_tasks();
                    Autoresize_texterea();
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

function Task_del(id) {
    $.ajax({
        type: "POST",
        url: "http://" + document.domain + "/ajax/delete-task",
        data: "id=" + id,
        success: function (msg) {
            if (msg) {
                try {
                    var res = JSON.parse(msg);
                    if (res.error) {
                        ShowErrorBlock(res.error);
                        return;
                    }
                    res.Status = function () {
                        return (this.status == 1) ? "checked" : "";
                    };
                    $(".projects_container").html(Mustache.render($('#template').html(), res));
                    Sortable_tasks();
                    Autoresize_texterea();
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

function Task_edit(elem) {
    var $per = $(elem).closest('.task-item');
    $per.find('.task_name').removeAttr('disabled').focus();
    $per.css("background-color", "#fcfed5");
    $('#addTodoList').attr('disabled', 'disabled');
    $("input[type=checkbox]").attr('disabled', 'disabled');
    $(".add_task_name").attr('disabled', 'disabled');
    $(".toolbar--task-list").attr("style", "visibility: hidden");
    $(".toolbar--header").attr("style", "visibility: hidden");
}

function Change_task_name(elem, id) {
    if ($(elem).val() != "") {
        $.ajax({
            type: "POST",
            url: "http://" + document.domain + "/ajax/rename-task",
            data: "id=" + id + "&name=" + $(elem).val(),
            success: function (msg) {
                if (msg) {
                    try {
                        var res = JSON.parse(msg);
                        if (res.error) {
                            ShowErrorBlock(res.error);
                            $(elem).val = 'Task';
                        }
                        $('#addTodoList').removeAttr('disabled', 'disabled');
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
}

function Lost_task_focus(elem) {
    if ($(elem).val() != "") {
        var $per = $(elem).closest('.task-item');
        $per.find('.task_name').attr('disabled', 'disabled');
        $per.css("background-color", "#fff")
        $('#addTodoList').removeAttr('disabled');
        $("input[type=checkbox]").removeAttr('disabled');
        $(".add_task_name").removeAttr('disabled');
        $(".toolbar--task-list").attr("style", "visibility: visible");
        $(".toolbar--header").attr("style", "visibility: visible");

    }
}

function Edit_status(elem, task_id) {
    var status = $(elem).prop("checked");
    if (status) {
        $(elem).closest('.task-item').find('.task_name').addClass('checked');
    }
    else {
        $(elem).closest('.task-item').find('.task_name').removeClass('checked');
    }
    $.ajax({
        type: "POST",
        url: "http://" + document.domain + "/ajax/edit-task-status",
        data: "id=" + task_id + "&status=" + status,

        success: function (msg) {
            if (msg) {
                try {
                    var res = JSON.parse(msg);
                    if (res.error) {
                        ShowErrorBlock(res.error);
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

function logout() {
    $.ajax({
        type: "POST",
        url: "http://" + document.domain + "/user/logout",
        success: function (msg) {
            if (msg) {
                try {
                    var res = JSON.parse(msg);
                    if (res) {
                        window.location.replace("/");
                    }
                    else {
                        ShowErrorBlock("SERVER ERROR");
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