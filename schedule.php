<?php
/*
 * St. Cecilia Schedule
 */
require_once('includes/header.php');
//require_once ('includes/ChromePhp.php');
ChromePHP::log("hallo");

// Determine if a class was clicked on for individual viewing
$cid = (isset($_GET['id'])) ? $_GET['id'] : 0;
$result = $db->get_class((int)$cid);
$cutoff = $db->get_class_cutoff($kwds['KWID']);


// Get a list of of teacher's classes
$myClasses[0]=-1;
if (isset($_SESSION['user_id'])) {
    $results = $db->get_teacher_classes($_SESSION['user_id'],$kwds['KWID']);
    $count = count($results);
    for ($i=0; $i<$count; $i++) {
        $myClasses[$i]=$results[$i]['class_id'];
    }
}

// Get the class information and store it in variables
$userClasses[0]=-1;
if (count($result) > 0) {
    $class_name = $result['ClassName'];
    $uid = $result['UserID'];
    $sca_name = $result['Title'] . ' ' . $result['SCAFirst'] . ' ' . $result['SCALast'];
    $mundane_name = $result['PrefixName'] . ' ' . $result['MundaneFirst'] . ' ' . $result['MundaneLast'];
    $room = $result['RoomName'];
    $roomID = $result['RoomID'];
    $desc = redisplay(redisplay($result['ClassDescription']));
    $start_time = date('l \a\t g:iA', (strtotime($result['day'])));
    $length = $result['hours'];
    $type = $result['TypeName'];
    $style = $result['StyleName'];
    $difficulty = $result['DifficultyName'];

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


    // Display the class information
    echo'
<div class="class_info">
    <h2>' . $class_name . '</h2>
    <ul>';
    $teachers= $db->get_class_teachers($cid);
    foreach ($teachers as $teacher) {
        $sca_name = $teacher['title'] . ' ' . $teacher['sca_first'] . ' ' . $teacher['sca_last'];
        $mundane_name = $teacher['PrefixName'] . ' ' . $teacher['first'] . ' ' . $teacher['last'];
        echo'
        <li><label class="bold">Instructor: </label> <a href="profile.php?id=' . $teacher['UserID'] . '">' . $sca_name;
        if ($mundane_name != "  ") {
            echo' (' . $mundane_name . ')';
        }
        echo'</a></li>';
    }
    if ($roomID!=0) {
        echo '<li><label class="bold">Schedule: </label> ' . $room . ' on ' . $start_time . '</li>';
    } else {
        echo '<li><label class="bold">Schedule: </label> Not Yet Scheduled</li>';
    }
        echo '<li><label class="bold">Class Type: </label> ' . $type . '</li>';
        echo '<li><label class="bold">Teaching Style: </label> ' . $style . '</li>';
        echo '<li><label class="bold">Difficulty: </label> ' . $difficulty . '</li>';
    echo '</ul> <p><li><label class="bold">Description: </label></li>'
     . $desc . '</p>
</div>
<div class="box warning">Other classes by the teacher(s) listed above are highlighted yellow below.</div>';
}
$where=array();
?>

<script language="JavaScript" type="text/javascript">

    function form_submit(id) {
        document.forms["class_form"].action="schedule.php?kwds=<?php echo $kwds['KWID']; ?>&id="+id;
        document.forms["class_form"].submit();
    }

    function my_schedule() {
        document.forms["class_form"].action="myschedule.php?kwds=<?php echo $kwds['KWID']; ?>";
        document.getElementById("button").submit();
        //document.forms["class_form"].submit();
    }

</script>
<?php // Display the legend and the schedule ?>
<h1>Schedule for St. Cecilia at the Tower <?echo roman($kwds['KWID']);?></h1>
<h2 class="printhide">Deadline for Class Submissions: <?php echo $cutoff;?></h2>
<?php
//if ($kwds['KWID'] == 10) {
//    echo '<p><a href="images/maps/KWDS10_SiteMap_01.png">Class Map 1</a></p>';
//}

/* Check for teacher schedule conflicts and show conflicting classes */
$conflicts = $db->check_conflicts($kwds['KWID']);
if (count($conflicts) > 0) {
    echo '<div class="box warning">This schedule is not finalized and is still subject to change.</div>';
}
?>
<form id="class_form" method="post">
<div class="schedule legend printhide">
    <div class="legend_th"><u>Legend</u><br />Black is not rated</div>
    <div class="wrapper">
    <div class="class vocal">Vocal Class<br />(Green Border)<br /><input type="checkbox" name="vocal" value="vocal" onclick="form_submit(0)"
        <?php if (isset($_POST['vocal'])) {echo 'checked="checked" '; array_push($where,array('type_id'=>'2'));} ?> />Hide</div>
    <div class="class instrumental">Instrumental Class<br /><br />(Red Border)<br /><input type="checkbox" name="instrumental" value="instrumental" onclick="form_submit(0)"
        <?php if (isset($_POST['instrumental'])) {echo 'checked="checked" ';array_push($where,array('type_id'=>'3'));} ?>/>Hide</div>
    <div class="class vocal_instrumental">Vocal & Instrumental<br />(Purple Border)<br /><input type="checkbox" name="vocal_instrumental" value="vocal_instrumental" onclick="form_submit(0)"
        <?php if (isset($_POST['vocal_instrumental'])) {echo 'checked="checked" '; array_push($where,array('type_id'=>'4'));} ?>/>Hide</div>
    <div class="class lecture">Lecture Class<br />(Lecture Icon)<br /><input type="checkbox" name="lecture" value="lecture" onclick="form_submit(0)" 
        <?php if (isset($_POST['lecture'])) {echo 'checked="checked" ';array_push($where,array('style_id'=>'2'));}?>/>Hide</div>
    <div class="class playing">Playing Class<br />(Playing Icon<br /><input type="checkbox" name="playing" value="playing" onclick="form_submit(0)"
        <?php if (isset($_POST['playing'])) {echo 'checked="checked" ';array_push($where,array('style_id'=>'1'));}?>/>Hide</div>
        <br>
    <div class="class level1" title="Level 1 - Class is appropriate for nonmusicians">Level 1 Class<br />(Green Text)<br /><input type="checkbox" name="level1" value="level1" onclick="form_submit(0)" 
        <?php if (isset($_POST['level1'])) {echo 'checked="checked" ';array_push($where,array('difficulty_id'=>'2'));}?>/>Hide</div>
    <div class="class level2" title="Level 2 - Class is geared toward those who have studied music for less than a year, or who make music only occasionally.">Level 2 Class<br />(Orange Text)<br /><input type="checkbox" name="level2" value="level2" onclick="form_submit(0)" 
        <?php if (isset($_POST['level2'])) {echo 'checked="checked" ';array_push($where,array('difficulty_id'=>'3'));} ?>/>Hide</div>
    <div class="class level3" title="Level 3 - Concepts/skills in this class are aimed at musicians who are comfortable with their instruments.">Level 3 Class<br />(Purple Text)<br /><input type="checkbox" name="level3" value="level3" onclick="form_submit(0)" 
        <?php if (isset($_POST['level3'])) {echo 'checked="checked" ';array_push($where,array('difficulty_id'=>'4'));}?>/>Hide</div>
    <div class="class level4" title="Level 4 - Experienced musicians may find this class challenging.">Level 4 Class<br />(Pink Text)<br /><input type="checkbox" name="level4" value="level4" onclick="form_submit(0)" 
        <?php if (isset($_POST['level4'])) {echo 'checked="checked" ';array_push($where,array('difficulty_id'=>'5'));}?>/>Hide</div>
    <div class="class level5" title="Level 5 - This class is very challenging and/or requires a significant amount of specialized knowledge as a prerequisite.">Level 5 Class<br />(Brown Text)<br /><input type="checkbox" name="level5" value="level5" onclick="form_submit(0)" 
        <?php if (isset($_POST['level5'])) {echo 'checked="checked" ';array_push($where,array('difficulty_id'=>'6'));}?>/>Hide</div>
    </div>
</div>
<?php

// Determines number of days that this St. Cecilia lasts
$kday = 0;
$keday = (date('z', strtotime($kwds['end_date'])) - date('z', strtotime($kwds['start_date'])));
$results = $db->get_rooms($kwds['KWID']);
for ($kday; $kday <= $keday; $kday++) {

    // Show message if there are no rooms submitted for this St. Cecilia
    if (count($results) < 1) {
        echo '<div class="box attention">There are no rooms available for this St. Cecilia yet.</div>';
        $kday=$keday+1;
    }

    else {
        $newdate=strtotime('+'.($kday)." day", strtotime($kwds['start_date']));
        echo '
<div class="schedule" ><h2>Day ' . ($kday+1) . ': '.date('l',$newdate).'</h2>
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

        foreach ($results as $result) {
            echo'
    <div class="tr">';
            $rooms = $db->get_class_rooms($result['id'], date('z', strtotime($kwds['start_date'])) + $kday + 2, $where);
            //print_r( $rooms);
            if (count($rooms) > 0) {
                echo'
        <div class="th">' . $result['name'] . '</div>';

                foreach ($rooms as $room) {
                    //if ((isset($_POST['checkboxes']) AND $_POST['c' . $room['ClassID']] == $room['ClassID'])) {
                   if (!$room['accepted']){ continue;}
                        echo '<div class="class';
                        if (in_array($room['ClassID'], $myClasses)){echo ' required';}
                        elseif (in_array($room['ClassID'], $userClasses)) {echo ' other';}
                        
                            switch ($room['TypeID']) {
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
                        
                        switch ($room['DifficultyID']) {
                            case 2:
                                echo ' level1';
                                break;
                            case 3:
                                echo ' level2';
                                break;
                            case 4:
                                echo ' level3';
                                break;
                            case 5:
                                echo ' level4';
                                break;
                            case 6:
                                echo ' level5';
                                break;
                            default:
                                break;
                        }
                        switch ($room['StyleID']) {
                            case 1:
                                echo ' playing';
                                break;
                            case 2:
                                echo ' lecture';
                                break;
                            default:
                                break;
                        }
                        $thistime = strtotime($room['day']);
                        echo '" style="width:' . ($room['hours'] * 1.1) . 'px; position: absolute; margin-left: ' . $room['time'] . 'px;"
                        title="' . $room['ClassName'] . ' ('.$room['user'].')'. ' - ' . $room['description'] . '">
                        <input class="mark" type="checkbox" name="c' . $room['ClassID'] . '" value="' . $room['ClassID'] . '"';
                        if ($_POST['c' . $room['ClassID']] == $room['ClassID']) {
                            echo ' checked="checked"';
                        }
                        echo ' /><a onclick="form_submit(' . $room['ClassID'] . ')"><div class="title">' . date('g:iA', $thistime) . ' ' . $room['ClassName'] . '</div>';
                        /*<div class="user"> ' . $room['user'] . '</div>*/ echo '</a></div>';
                    //}
                }
            }
            echo'
    </div>';
        }
        echo'
</div>';
    }
}
?>
<div class="schedule wrapper" >
    <div class="legend_th"><u>Level Descriptions</u></div>
<div class="" style="text-align:left;display:inline-block;">
<ul class="level" >
<li class="level level1">Level 1 - Class is appropriate for nonmusicians. </li>
<li class="level level2">Level 2 - Class is geared toward those who have studied music for less than a year, or who make music only occasionally.</li>
<li class="level level3">Level 3 - Concepts/skills in this class are aimed at musicians who are comfortable with their instruments.</li>
<li class="level level4">Level 4 - Experienced musicians may find this class challenging.</li>
<li class="level level5">Level 5 - This class is very challenging and/or requires a significant amount of specialized knowledge as a prerequisite.</li>

</ul>
</div>
</div>
<?php

if (count($results) > 0) {
    echo '<p class="printhide">For a personalized schedule, check each class that you want to take above, then click the button below.
        It will show you the classes you are interested in taking in chronological order.</p>
    <input id="button" type="submit" class="button center" name="checkboxes" onclick="my_schedule()" value="Show My Schedule" />
';}
echo '</form>';

$classes = $db->get_unscheduled_classes($kwds['KWID']);

if (count($classes) > 0) {
    echo '<div class="schedule printhide"><h2>Unscheduled Classes</h2>';
    if (is_class_scheduler($_SESSION['user_id'],$kwds['KWID'])) {
        echo '    <div class="warning margins box">Highlighted classes have not been approved and are not visible to general users.</div>';
    }

    foreach ($classes as $class) {
        echo '
<a href="schedule.php?kwds=' . $kwds['KWID'] . '&id=' . $class['ClassID'] . '" style="margin-left:1px"><div class="class';
        switch ($class['TypeID']) {
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
        switch ($class['DifficultyID']) {
            case 2:
                echo ' level1';
                break;
            case 3:
                echo ' level2';
                break;
            case 4:
                echo ' level3';
                break;
            case 5:
                echo ' level5';
                break;
            case 6:
                echo ' level6';
                break;
            default:
                break;
        }
        switch ($room['StyleID']) {
            case 1:
                echo ' playing';
                break;
            case 2:
                echo ' lecture';
                break;
            default:
                break;
        }
        if ($class['accepted']!=1) {
            echo ' highlight';
        }
        echo '" style="width:' . ($class['hours'] * 1.1) . 'px;" >' . $class['name'] . '<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</div></a>';
    }
    echo '</div>';
}

include_once('includes/footer.php');
?>
