<?php
/*
 * Master Schedule
 */
include_once 'includes/header.php';

if (isset($_POST['activity']) AND $_POST['activity']== '') {
    echo '<div class="box error">You must enter a description for the activity.</div>';
}
elseif (isset($_POST['activity']) AND is_autocrat($_SESSION['user_id'],$kwds['KWID'])) {
    if ($_POST['beginPM']==1 AND $_POST['beginHour'] != 12) { $_POST['beginHour']=$_POST['beginHour']+12;}
    if ($_POST['endPM']==1 AND $_POST['endHour'] != 12) { $_POST['endHour']=$_POST['endHour']+12;}
    $begin = date('Y-m-d H:i:s',mktime($_POST['beginHour'],$_POST['beginMinute'],0,date('m',strtotime($_POST['date'])),date('d',strtotime($_POST['date'])),date('Y',strtotime($_POST['date']))));
    If ($_POST['endHour'] < $_POST['beginHour']) {
        $end = date('Y-m-d H:i:s',mktime($_POST['endHour'],$_POST['endMinute'],0,date('m',strtotime($_POST['date'])),date('d',strtotime($_POST['date']))+1,date('Y',strtotime($_POST['date']))));
    }
    Else {
        $end = date('Y-m-d H:i:s',mktime($_POST['endHour'],$_POST['endMinute'],0,date('m',strtotime($_POST['date'])),date('d',strtotime($_POST['date'])),date('Y',strtotime($_POST['date']))));
    }
//    $estimate = $_POST['estimate'];
    $name = $_POST['activity'];
//    $ordinal = (int)$_POST['ordinal'];
    $room = $_POST['room'];
//    $useEnd = isset($_POST['useEnd'])? $_POST['useEnd']:0;
    $db->insert_master_schedule($name, $kwds['KWID'], $room, $begin, $end); //, $estimate, $useEnd, $ordinal);
    echo '<div class="box success">The activity has been added to the master schedule.</div>';
}
elseif (isset($_POST['delete']) AND is_autocrat($_SESSION['user_id'],$kwds['KWID'])) {
    $db->delete_master_schedule($_POST['selection']);
    echo '<div class="box success">The activity has been REMOVED from the schedule.</div>';
}
echo '<h1>Master Schedule</h1><br />';
$results = $db->get_master_schedule($kwds['KWID']);
echo '<form action="master.php" method="post" class="form"><table><tr><th>Time</th><th>Location</th><th>Activity</th>';
$day = '';
foreach ($results as $result) {
    if ($result['beginDay'] != $day) {
        echo '<tr><td>&nbsp;</td></tr><tr><th style="text-align:left;">'.$result['beginDay'].'</th></tr>';
        $day = $result['beginDay'];
    }
    echo '<tr><td style="text-align:center;">';
    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$result['beginTime'];
    if ($result['begin'] < $result['end']) {
        echo ' - '.$result['endTime'];
    }
    if ($result['place']=="n/a") {$result['place']=' ';}
    echo '&nbsp;&nbsp;&nbsp;&nbsp;</td><td>'.$result['place'].'&nbsp;&nbsp;&nbsp;&nbsp;</td><td>';
    if ((is_autocrat($_SESSION['user_id'],$kwds['KWID']) AND $db->get_next_kwds() <= $kwds['KWID']) OR is_super_user($_SESSION['user_id'])) {
        echo '<input class="printhide" type="radio" group="activity" name="selection" style="width:20px;" value="'.$result['id'].'" />';
    }
    echo '<span class="cap">'.$result['event'].'</span></td></tr>';
    
}
echo '</table>';
if ((is_autocrat($_SESSION['user_id'],$kwds['KWID']) AND $db->get_next_kwds() <= $kwds['KWID']) OR is_super_user($_SESSION['user_id'])) {
    echo '<br><input type="submit" value="Remove Selection From Schedule" class="button printhide" name="delete" />';
}
echo '</form><br /><br />';
if ((is_autocrat($_SESSION['user_id'],$kwds['KWID']) AND $db->get_next_kwds() <= $kwds['KWID']) OR is_super_user($_SESSION['user_id'])) { ?>
<form action="master.php?kwds=<?php echo $kwds['KWID']; ?>" method="post" class="form printhide">
    <h1>Add New Activity to Schedule</h1>
    <ul>
        <li><label>Activity Description:</label><input type="text" name="activity" /></li>
        <li><label for="room">Room:</label><?php $result=$db->get_rooms($kwds['KWID']); $result[count($result)]['id']=0;
            $result[count($result)-1]['name']='n/a'; dropdown($result, 'room', $room_id) ?></li>
        <li><label>Begin Date: </label><?php get_event_dates($cdate); /*name="date"*/ ?></li>
<!--        <li><label></label><input type="checkbox" value="1" name="estimate" <?php if ($estimate==1) {echo 'checked="checked" ';} ?> style="width:20px;" />This is an Estimated Time</li>-->
        <li><label for="begin">Start Time:</label><?php dropdown_num('beginHour', 1, 12, 1,$hr); echo ' : '; dropdown_num('beginMinute', 0, 55, 5, $min); ?>
            <select name="beginPM" style="width:60px;"><option value="0">AM</option><option value="1">PM</option></select></li>
<!--        <li><label></label><input type="checkbox" value="1" name="estimate" <?php if ($useEnd==1) {echo 'checked="checked" ';} ?> style="width:20px;" />Check to Use End Time</li>-->
        <li><label for="end">End Time:</label><?php dropdown_num('endHour', 1, 12, 1,$hr); echo ' : '; dropdown_num('endMinute', 0, 55, 5, $min); ?>
            <select name="endPM" style="width:60px;"><option value="0">AM</option><option value="1">PM</option></select></li>
<!--        <li><label for="ordinal">Ordinal:</label><?php dropdown_num('ordinal', 1, 10, 1);?>(only used if two items start at the same time)</li>-->
        <li><label></label><input type="submit" class="button" name="schedule" value="Add New Activity" /></li>
    </ul>
</form>

<?php }

include_once 'includes/footer.php';
?>
