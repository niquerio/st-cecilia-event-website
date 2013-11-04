<?php
/*
 * KWDS Schedule
 */
//require_once('includes/ChromePhp.php');
require_once('includes/header.php');
?>
<script>
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
</script>
<?php

// Make sure the user is logged in as a user that can edit the schedule
if (!(can_add_rooms($_SESSION['user_id'],$kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds() OR is_super_user())) {
    echo '<div class="box error">You do not have permissions to view this page.</div>';
    redirect('index',$kwds['KWID']);
    include_once('includes/footer.php');
    die;
}
// If the "UNSCHEDULE" button was pressed, remove that class from the schedule
if (isset($_POST['unschedule'])) {
    $db->remove_from_schedule($_POST['cid']);
    echo '<div class="box success">The class was removed from the schedule.</div>';
}
/* Update class submission */
if (isset($_POST['remove'])) {
    $cid = $_POST['cid'];
    $db->remove_class($cid);
    echo '<div class="box success">The Class Has been Removed</div>';
}
// If the "ADD TO SCHEDULE" button was pressed, update the class info
if (isset($_POST['class'])) {

    $accept=$_POST['accept'];
    $cid=$_POST['cid'];
    $name=sanit($_POST['name']);
    $desc=sanit($_POST['desc']);
    $hours=$_POST['hours']*60+$_POST['minutes'];
    $style=$_POST['style'];
    $type=$_POST['type'];
    $room=($accept!=0)?$_POST['room']:0;
    //$aero=$_POST['aerobic'];
    $diff=$_POST['difficulty'];
    $time=($_POST['hour']<8)?$_POST['hour']+12:$_POST['hour'];
   // $era=$_POST['era'];
    $date=$_POST['date'].' '.$time.':'.$_POST['minute'].':00';
    //$db->update_class($accept,$aero,$cid,$date,$desc,$diff,$era,$fee,$hours,$name,$room,$style,$type);
    $db->update_class($accept,$cid,$date,$desc,$diff,$fee,$hours,$name,$room,$style,$type);
}

$cday=isset($_POST['cday'])?$_POST['cday']:date('j', strtotime($kwds['class_date']));
$cmonth=isset($_POST['cmonth'])?$_POST['cmonth']:date('n', strtotime($kwds['class_date']));
$cyear=isset($_POST['cyear'])?$_POST['cyear']:date('Y', strtotime($kwds['class_date']));

/* Update class submission */
if (isset($_POST['submit'])) {
    $class_date=$cyear.'-'.$cmonth.'-'.$cday.' 00:00:00';
    $db->update_classSubmissionDate($class_date, $kwds['KWID']);
    echo '<div class="box success">The Class Submission Cut-off Date has been updated!</div>';
}



/* Check for teacher schedule conflicts and show conflicting classes */
$conflicts = $db->check_conflicts($kwds['KWID']);
if (count($conflicts) > 0) {
    echo '<div class="box error">A scheduling conflict has been found!</div>';
}

// Checks for class conflicts
$userClasses[0]=-1;
$badID[0] = -1;
$offSite[0] = -1;
$count = count($conflicts);
if ($count>0) {
    for ($i=0; $i<$count; $i++) {
        $badID[$i]=$conflicts[$i]['id'];
    }
}

// Check for teacher arrival and departure dates
$results = $db->check_attendance($kwds['KWID']);
$count = count($results);
for ($i=0; $i<$count; $i++) {
    $offSite[$i]=$results[$i]['id'];
}
if ($count > 0) {echo '<div class="box error">A class has been scheduled when the teacher will not be on site.</div>';}

/* Checks if class ID was entered into URL and gets its info */
$cid = (isset($_GET['id'])) ? $_GET['id'] : 0;
$result = $db->get_class($cid);
if (count($result) > 0) {
    $accept = $result['accepted'];
    //$aero= $result['AerobicID'];
    $cdate= date('Y-m-d', (strtotime($result['day'])));
    $class_name = $result['ClassName'];
    $desc = redisplay($result['ClassDescription']);
    $diff= $result['DifficultyID'];
    $era= $result['EraID'];
    $hr = date('g', (strtotime($result['day'])));
    $length = $result['hours'];
    /**/$hour = intval($length/60);
    /**/$minute = $length%60;
    $min = date('i', (strtotime($result['day'])));
    $mundane_name = $result['PrefixName'] . ' ' . $result['MundaneFirst'] . ' ' . $result['MundaneLast'];
    $notes = $result['other'];
    $room = $result['RoomName'];
    $room_id= $result['RoomID'];
    $sca_name = $result['Title'] . ' ' . $result['SCAFirst'] . ' ' . $result['SCALast'];
    $style = $result['StyleID'];
    $type = $result['TypeID'];
    $uid = $result['UserID'];

    /*Gets a list of classes from the selected teacher(s)*/
    $teachers=$db->get_class_teachers($cid);
    $count = count($teachers);
    $users='-1';
    for ($i=0; $i<$count; $i++) {
        $users=$users.','.$teachers[$i]['UserID'];
    }
    $results=$db->get_teacher_classes($users, $kwds['KWID']);
    $count = count($results);
    for ($i=0; $i<$count; $i++) {
        $userClasses[$i]=$results[$i]['class_id'];
    }
?>

<!-- Displays class info so it can be added to the schedule -->
<form class="form" action="class_schedule.php?kwds=<?php echo $kwds['KWID'] ?>" method="post">
<div class="class_info">
    <ul>
        <?php if ($notes !='') { echo '<li><div class="error box">'.$notes.'</div></li>'; }; ?>
        <li id="current_class_teachers"><label for="teacher">Teacher(s):</label><?php
        foreach ($teachers as $teacher) {
            echo '<p id="current_class_teacher_'.$teacher['UserID'].'"><label></label>';
            echo '<a href="profile.php?id='.$teacher['UserID'].'">'.$teacher['sca_first'].' '.$teacher['sca_last'];
            if ($teacher['first']!="") {
                echo '('.$teacher['first'].' '.$teacher['last'].')';
            }
            echo '</a> [<a href="javascript:void()" onClick="remove_teacher_from_class(' .$cid. ',' .$teacher['UserID'].')">Remove</a>]</p>';
        }
        
        //$db=new db; $resul=$db->get_list('user'); dropdown($result, 'user'
    echo('<p id="show_hide_teacher_search"><a href="javascript:void()" onClick="search_for_teacher(\''.$cid.'\')">Add Teacher</a></p></li>')?>
        <li><label for="name">Class Name:</label><input type="text" name="name"<?php echo 'value="'.$class_name.'"'; ?> /></li>
<!--        <li><label>Teacher:</label><?php echo $sca_name; if ($mundane_name!="  ") { echo' ('.$mundane_name.' )'; } ?></li>-->
        <li><label for="desc">Class Description:</label><textarea name="desc" cols="50" rows="10"><?php echo $desc ?></textarea></li>
        <li><label for="hours">Length of Class:</label><?php dropdown_num('hours', 0, 8, 1,$hour); echo 'Hrs '; dropdown_num('minutes', 0, 55, 5, $minute); echo 'Minutes'; ?></li>
        <li><label for="difficulty">Suggested Skill Level:</label><?php $db=new db; $result=$db->get_list('difficulty'); dropdown($result, 'difficulty', $diff) ?></li>
      <!--  <li><label for="aerobic">Aerobic Level:</label><?php// $result=$db->get_list('aerobic'); dropdown($result, 'aerobic', $aero) ?></li> -->
        <!--<li><label for="era">Time Period:</label><?php// $result=$db->get_list('era'); dropdown($result, 'era', $era) ?></li>-->
        <li><label for="type">Type of Class:</label><?php $result=$db->get_list('type'); dropdown($result, 'type', $type) ?></li>
        <li><label for="style">Teaching Style:</label><?php $result=$db->get_list('style'); dropdown($result, 'style', $style) ?></li>
        <li><label for="room">Room:</label><?php $result=$db->get_rooms($kwds['KWID']); $result[count($result)]['id']=0;
            $result[count($result)-1]['name']='n/a'; dropdown($result, 'room', $room_id) ?></li>
        <li><label for="hours">Start Time:</label><?php dropdown_num('hour', 1, 12, 1,$hr); echo ' : '; dropdown_num('minute', 0, 55, 5, $min); ?></li>
        <li><label>Date: </label><?php get_event_dates($cdate); /*name="date"*/ ?></li>
        <li><label>Accepted:</label><input type="checkbox" value="1" name="accept" <?php if ($accept==1) {echo 'checked="checked" ';} ?>/></li>
        <li><label></label><input type="submit" class="button" name="class" value="Update" /></li>
        <li><label></label><input type="submit" class="button" name="remove" value="Remove Class" /></li>
    </ul>
    <input type="hidden" name="kwds" value="<?php echo $kwds['KWID'] ?>" />
    <input type="hidden" name="cid" value="<?php echo $cid ?>" />
</div>
</form>
<?php } ?>

<h1>Schedule</h1>
<div class="schedule">
    <div class="legend_th"><u>Legend</u><br />Black is not rated<br />&nbsp;</div>
    <div class="wrapper">
    <div class="class vocal">Vocal Class<br />(Blue Border)<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</div>
    <div class="class instrumental">Instrumental Class<br />(Red Border)<br />&nbsp;<br />&nbsp;<br />&nbsp;</div>
    <div class="class vocal_instrumental">Instrumental and Vocal Class<br />(Purple Border)<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</div>
    <div class="class beg">Beginner's Class<br />(Green Text)<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</div>
    <div class="class int">Intermediate Class<br />(Blue Text)<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</div>
    <div class="class adv">Advanced Class<br />(Red Text)<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</div>
    <div class="class lecture">Lecture Class<br />(Lecture Icon)</div>
    <div class="class playing">Playing Class<br />(Playing Icon)</div>
    </div>
</div>
<?php
$keday = (date('z', strtotime($kwds['end_date'])) - date('z', strtotime($kwds['start_date'])));
for ($kday=0; $kday <= $keday; $kday++) {
    $result = $db->get_rooms($kwds['KWID']);
    if (count($result) < 1) {
        echo '<p>There are no classes scheduled yet for day ' . ($kday + 1) . '.</p>';
    } else {
        echo '
<div class="schedule" ><h2>Day ' . ($kday + 1)/* date('l', strtotime('+'.($kday).' days',$kwds['start_date'])) */ . '</h2>
    <div class="tr">
        <div class="time">TIME</div>';
        for ($i = 9; $i != 6; $i++) {
            echo '
        <div class="hour">' . $i . ':00</div>';
            if ($i >= 12) {
                $i = 0;
            }
        }
        echo '
    </div>';
        foreach ($result as $row) {
            echo'
    <div>';
            $room = $db->get_class_rooms($row['id'], date('z', strtotime($kwds['start_date'])) + $kday + 2);
        ChromePHP::log("class: " . $room);
            if (count($room) > 0) {
                echo'
        <div class="th">' . $row['name'] . '</div>';
                foreach ($room as $rooms) {
                    //echo 'other='.$rooms['other'];
                    echo '<a title="'.redisplay($rooms['other']).'" href="class_schedule.php?kwds=' . $kwds['KWID'] . '&id=' . $rooms['ClassID'] . '">
                        <div class="class';
                    if (in_array($rooms['ClassID'], $badID)){echo ' conflict';}
                    elseif (in_array($rooms['ClassID'], $offSite)) {echo ' conflict';}
                    elseif (in_array($rooms['ClassID'], $userClasses)){echo ' required';}
                    
                        switch ($rooms['TypeID']) {
                            case 2:
                                echo ' vocal';
                                break;
                            case 3:
                                echo ' instrumental';
                                break;
                            case 4:
                                echo ' vocal_instrumental';
                                break;
                            default:
                                break;
                        }
                    switch ($rooms['DifficultyID']) {
                        case 2:
                            echo ' beg';
                            break;
                        case 3:
                            echo ' int';
                            break;
                        case 4:
                            echo ' adv';
                            break;
                        default:
                            break;
                    }
                    switch ($rooms['StyleID']) {
                        case 1:
                            echo ' playing';
                            break;
                        case 2:
                            echo ' lecture';
                            break;
                        default:
                            break;
                    }
                    $thistime = strtotime($rooms['day']);
                    echo '" style="width:' . ($rooms['hours'] * 1.1) . 'px; position: absolute; margin-left: ' . $rooms['time'] . 'px;"
                        title="['.$rooms['user'].'] '. $rooms['description'] . '">
                        <input class="mark" type="checkbox" /><div class="title">' . date('g:iA', $thistime) . ' ' . $rooms['ClassName'] . '</div>
                        <div class="user"> </div></div></a>';
                }
            }
            echo'
    </div>';
        }
        echo'
</div>';
    }
}
echo '<div class="schedule"><h2>Unscheduled Classes</h2>
    <div class="warning margins box">Highlighted classes have not been approved and are not visible to general users.</div>';
$classes = $db->get_unscheduled_classes($kwds['KWID']);

if (count($classes) > 0) {
    foreach ($classes as $row) {
                    echo '<a title="'.$row['other'].'" style="margin-left:1px" href="class_schedule.php?kwds=' . $kwds['KWID'] . '&id=' . $row['ClassID'] . '"><div class="class';
                    switch ($row['TypeID']) {
                        case 2:
                            echo ' vocal';
                            break;
                        case 3:
                            echo ' instrumental';
                            break;
                        case 4:
                            echo ' vocal_instrumental';
                            break;
                        default:
                            break;
                    }
                    switch ($row['DifficultyID']) {
                        case 2:
                            echo ' beg';
                            break;
                        case 3:
                            echo ' int';
                            break;
                        case 4:
                            echo ' adv';
                            break;
                        default:
                            break;
                    }
                    switch ($row['StyleID']) {
                        case 1:
                            echo ' playing';
                            break;
                        case 2:
                            echo ' lecture';
                            break;
                        default:
                            break;
                    }
                    if ($row['accepted']==0) {
                        echo ' highlight';
                    }
echo '" style="width:' . ($row['hours'] * 1.1) . 'px;" >' . $row['name'] . '<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</div></a>';
    }
}
echo'</div>';
echo '<form class="form" action="class_schedule.php?kwds='.$kwds['KWID'].'" method="post">';
echo '<h2>Special Notes for Classes</h2>';
$notes = $db->get_special_notes($kwds['KWID']);
if  (count($notes)>0 ){
    foreach ($notes as $note) {
        echo '<p><b>'.$note['name'].'</b> - '.$note['other'].'</p>';
    }
}
echo '<h2>Class Submission Cutoff Date</h2>
        <ul>
        <li><label></label>';
echo dropdown_num('cmonth',1,12, 1,$cmonth);
echo dropdown_num('cday', 1, 31,1,$cday);
echo dropdown_num('cyear', 1997,date('Y',time())+5,1,$cyear);
echo '</li>
        <li><label></label><input type="submit" name="submit" value="Update" /></li>
        </ul>
    
    </form>';

include_once('includes/footer.php');

?>
