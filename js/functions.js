function remove_teacher_from_class(cid,teacher_id){
    //alert(cid + " " + teacher_id);
    var curr_teacher_selector = "#current_class_teacher_" + teacher_id;
    $.ajax({
        url:"lib/remove_teacher.php", 
        data: {cid: cid, teacher_id: teacher_id},
        type: "POST",
        dataType: 'text',
        success: function(data){
            $(curr_teacher_selector).remove();
        }
    });
}
var teacher_to_add = null;

function search_for_teacher(cid){
    $("#show_hide_teacher_search").hide();
var current_class_teachers = $("#current_class_teachers");
var div = $("<div>").attr("id","teacher_search_div");
$("<label>").attr("for","teacher_search").text("Teachers: ").appendTo(div);
$("<input>").attr("id","teacher_search").appendTo(div);
$("<input>").attr("type","submit").click(function(event){ event.preventDefault(); add_teacher_to_class(cid)}).appendTo(div);
$(div).appendTo(current_class_teachers);
$("<a>").attr("href","javascript:void()").text("Hide Teacher Add Search").click(function(){
    $("#show_hide_teacher_search").show();
    $("#teacher_search_div").remove();
}).appendTo(div)

    $.ajax({
        url:"lib/get_users_not_teaching_class.php", 
        data: {cid: cid},
        type: "POST",
        dataType: 'json',
        success: function(data){
        
            $("#teacher_search").autocomplete({
                source: data, 
                change: function(event, ui){
                     if(ui.item === null){
                         teacher_to_add = null;
                     }
                     else{ teacher_to_add = ui.item.id;}  
                },
            });
        },
            
        });

}
$("#teacher_search").click(function(){
    $("#teacher_search").trigger("focus");
});

function  add_teacher_to_class(cid){
    if(teacher_to_add === null) throw("Not a valid teacher");

    $.ajax({
        url:"lib/add_teacher_to_class.php", 
        data: {cid: cid, teacher_id: teacher_to_add},
        type: "POST",
        dataType: 'text',
        success: function(data){
            location.reload();
        }
    });

}

