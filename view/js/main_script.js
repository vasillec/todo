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

$(function()
{
    $.ajax({
        type: "POST",
        url: "controller/base.php",
        data: "&method=get_project",

        success:function(msg)
        {
            var res = JSON.parse(msg);
            res.Status = function (){return (this.status==1)? "checked" : "";} ;
            $(".projects_container").html(Mustache.render($('#template').html(), res));
            Sortable_tasks();
            Autoresize_texterea();
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
        axis:'y',
        cursor:'move',
        handle:'.drag-task',
        opacity:0.9,
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
                url: "controller/base.php",
                data: "arr="+order+"&method=sort_tasks",

                success:function(msg)
                {
                    if(msg) ShowErrorBlock(msg);
                }
            });
        }
    }
    );
}

function Project_edit(elem)
{
    var inp = $(elem).closest('.project-header').find('.project_name');
    inp.removeAttr('disabled');
    inp.focus();
    $('#addTodoList').attr('disabled','disabled');
    $("input[type=checkbox]").attr('disabled','disabled');
    $(".add_task_name").attr('disabled','disabled');
    $(".toolbar--task-list").attr("style", "visibility: hidden");
    $(".toolbar--header").attr("style", "visibility: hidden");
}

function Save_add_project(name)
{
    if(name.length>0)
    {
        $.ajax({
            type: "POST",
            url: "controller/base.php",
            data: "name="+name+"&method=add_project",

            success:function(msg)
            {
                var res = JSON.parse(msg);
                $(".delete_after").remove();
                res.Status = function (){return (this.status==1)? "checked" : "";} ;
                $(".projects_container").append(Mustache.render($('#template').html(), res));
                $('#addTodoList').removeAttr('disabled','disabled');
                Sortable_tasks();
                Autoresize_texterea();
            }
        });
    }
    else
    {
        $(".delete_after").remove();
        $('#addTodoList').removeAttr('disabled','disabled');
    }

}

function Lost_focus(elem)
{
    if($(elem).val() != "") {
        $(elem).attr('disabled', 'disabled');
        $('#addTodoList').removeAttr('disabled');
        $("input[type=checkbox]").removeAttr('disabled');
        $(".add_task_name").removeAttr('disabled');
        $(".toolbar--task-list").attr("style", "visibility: visible");
        $(".toolbar--header").attr("style", "visibility: visible");
    }
}

function Change_project_name(elem, id)
{
    if($(elem).val() != "") {
        $.ajax({
            type: "POST",
            url: "controller/base.php",
            data: "id="+id+"&name="+$(elem).val()+"&method=rename_project",

            success:function(msg)
            {

                $('#addTodoList').removeAttr('disabled','disabled');
            }
        });
    }
}

function project_delete (id)
{
    $.ajax({
        type: "POST",
        url: "controller/base.php",
        data: "id="+id+"&method=delete_project",

        success:function(msg)
        {
            var res = JSON.parse(msg);
            console.log(msg);
            res.Status = function (){return (this.status==1)? "checked" : "";} ;
            $(".projects_container").html(Mustache.render($('#template').html(), res));
            Sortable_tasks();
            Autoresize_texterea();
        }
    });
}

function Add_project()
{
    $('#addTodoList').attr('disabled','disabled');
    var json = {projects:[{"name":""}]};
    $(".projects_container").append(Mustache.render($('#template_add_project').html(), json));
    $('.project_name_add').focus();
}

function Validation_task(elem)
{
    if($(elem).val() != "")
    {
        $(elem).parent().find('#add_task_btn').removeAttr('disabled', 'disabled');
    }
    else
    {
        $(elem).parent().find('#add_task_btn').attr('disabled', 'disabled');
    }
}

function Add_task(elem, id)
{
    var name = $(elem).parent('.edit-form--task').children('.add_task_name').val();
    $.ajax({
        type: "POST",
        url: "controller/base.php",
        data: "id="+id+"&name="+name+"&method=add_task",

        success:function(msg)
        {
            var res = JSON.parse(msg);
            console.log(msg);
            res.Status = function (){return (this.status==1)? "checked" : "";} ;
            $(".projects_container").html(Mustache.render($('#template').html(), res));
            Sortable_tasks();
            Autoresize_texterea();
        }
    });
}

function Task_del(id)
{
    $.ajax({
        type: "POST",
        url: "controller/base.php",
        data: "id="+id+"&method=del_task",

        success:function(msg)
        {
            var res = JSON.parse(msg);
            console.log(msg);
            res.Status = function (){return (this.status==1)? "checked" : "";} ;
            $(".projects_container").html(Mustache.render($('#template').html(), res));
            Sortable_tasks();
            Autoresize_texterea();
        }
    });
}

function Task_edit(elem)
{
    var $per =  $(elem).closest('.task-item');
    $per.find('.task_name').removeAttr('disabled').focus();
    $per.css("background-color", "#fcfed5")
    $('#addTodoList').attr('disabled','disabled');
    $("input[type=checkbox]").attr('disabled','disabled');
    $(".add_task_name").attr('disabled','disabled');
    $(".toolbar--task-list").attr("style", "visibility: hidden");
    $(".toolbar--header").attr("style", "visibility: hidden");
    
}

function Change_task_name(elem, id)
{
    if($(elem).val() != "") {
        $.ajax({
            type: "POST",
            url: "controller/base.php",
            data: "id="+id+"&name="+$(elem).val()+"&method=rename_task",
            success:function(msg)
            {
                $('#addTodoList').removeAttr('disabled','disabled');
            }
        });
    }
}

function Lost_task_focus(elem)
{
    if($(elem).val() != "") {
       var $per =  $(elem).closest('.task-item');
       $per.find('.task_name').attr('disabled','disabled');
       $per.css("background-color", "#fff")
       $('#addTodoList').removeAttr('disabled');
       $("input[type=checkbox]").removeAttr('disabled');
       $(".add_task_name").removeAttr('disabled');
       $(".toolbar--task-list").attr("style", "visibility: visible");
       $(".toolbar--header").attr("style", "visibility: visible");

   }
}

function Edit_status(elem, task_id)
{
    var status = $(elem).prop("checked");
    if(status)
    {
        $(elem).closest('.task-item').find('.task_name').addClass('checked');
    }
    else
    {
        $(elem).closest('.task-item').find('.task_name').removeClass('checked');
    }
    $.ajax({
        type: "POST",
        url: "controller/base.php",
        data: "id="+task_id+"&status="+status+"&method=edit_status",

        success:function(msg)
        {

        }
    });
}
