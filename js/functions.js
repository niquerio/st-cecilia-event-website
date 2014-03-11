//New Classes
//Get all possible Teachers put in potential_teacher array
//When selected, remove selected from potential_teacher
//when removed, put removed back into potential_teacher
//Have way to add teacher info to class creation.

var users = ''; 
var current_users = ''; 
var teacher_to_add = null;

function deepCopy(obj) {
    if (Object.prototype.toString.call(obj) === '[object Array]') {
        var out = [], i = 0, len = obj.length;
        for ( ; i < len; i++ ) {
        out[i] = arguments.callee(obj[i]);
        }
        return out;
    }
    if (typeof obj === 'object') {
        var out = {}, i;
        for ( i in obj ) {
        out[i] = arguments.callee(obj[i]);
        }

        return out;
    }
    return obj; 
}
function load_users(){
    $.ajax({
        url:"lib/get_users.php", 
        type: "POST",
        dataType: 'json',
        async: false,
        success: function(data){
            users = data;
            current_users = deepCopy(data);
        
        },
            
        });
}
function add_potential_teacher_name(teacher_id){
    var teacher_name = '';
    for( var i=0; i < users.length; i++){
        if( users[i].id == teacher_id){
           teacher_name = users[i].label;
           break;
        }
    }
    if( teacher_name == '') return;

    var current_class_teachers = $("#current_class_teachers");
    var p_id = "current_class_teacher_" + teacher_id;

    var p = $("<p>");
    var label = $("<label>").attr("for",p_id).appendTo(p);
    var input = $("<input>").attr("id",p_id).attr("name","teachers[]").attr("type","hidden").val(teacher_id).appendTo(p)
    $("<a>").attr("href",'profile.php?id=' + teacher_id).text(teacher_name).appendTo(p);
    p.append(" [");
    $("<a>").attr("href","javascript:void()").text("Remove").click(function(){
        remove_potential_teacher($(this).prev().prev().val());
    }).appendTo(p);
    p.append("]");


    $(p).appendTo(current_class_teachers);

    for( var i=0; i < current_users.length; i++){
        if( current_users[i].id == teacher_id){
            current_users.splice(i,1);
           break;
        }
    }
}

function remove_potential_teacher(teacher_id){
    $("#current_class_teacher_" + teacher_id).parent().remove();
    for( var i=0; i < users.length; i++){
        if( users[i].id == teacher_id){
            current_users.push(users[i]);
            $("#teacher_search_div").remove();
            $("#show_hide_teacher_search").show();
           break;
        }
    }
}

function search_potential_teachers(){
    $("#show_hide_teacher_search").hide();
    var current_class_teachers = $("#current_class_teachers");
    var div = $("<div>").attr("id","teacher_search_div");
    $("<label>").attr("for","teacher_search").text("Teachers: ").appendTo(div);
    $("<input>").attr("id","teacher_search").appendTo(div);
    $("<input>").attr("type","submit").click(function(event){ 
        event.preventDefault(); 
        add_potential_teacher_name(teacher_to_add);
        $("#teacher_search_div").remove();
        $("#show_hide_teacher_search").show();
    }).appendTo(div);
    $(div).appendTo(current_class_teachers);
    $("<a>").attr("href","javascript:void()").text("Hide Teacher Add Search").click(function(){
        $("#show_hide_teacher_search").show();
        $("#teacher_search_div").remove();
    }).appendTo(div);
   $("#teacher_search").autocomplete({
       source: current_users, 
       //source: users, 
       change: function(event, ui){
            if(ui.item === null){
                teacher_to_add = null;
            }
            else{ teacher_to_add = ui.item.id;}  
       },
   });

}



//Dealing with already established classes
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

