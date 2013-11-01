<?php

/*
 * KWDS Class Listing
 */
require_once('includes/header.php');

echo'<h1>Classes</h1>';
$result = $db->get_class_info($kwds['KWID']);
if (count($result) < 1) {
    echo'<p>There are no classes scheduled for this event yet.</p>';
} else {
    echo'<p>There are ' . count($result) . ' classes.</p>';

    foreach ($result as $row) {
        $class_name = $row['ClassName'];
        $cid = $row['ClassID'];
        $uid = $row['UserID'];
        $sca_name = $row['TitleName'] . ' ' . $row['SCAFirst'] . ' ' . $row['SCALast'];
        $mundane_name = $row['PrefixName'] . ' ' . $row['MundaneFirst'] . ' ' . $row['MundaneLast'];
        $room = $row['RoomName'];
        $desc = redisplay($row['ClassDescription']);
        $start_time = date('l \a\t g:iA', (strtotime($row['day'])));
        $length = $row['hours'];
        $end_time = date('g:iA',strtotime('+ '.$length.' minute',strtotime($row['day'])));
        //strtotime('+59 minutes', strtotime('2011-11-17 05:05'));
        $type = $row['TypeID'];
        echo'
<div class="class_info">
    <h2>' . $class_name . '</h2>
    <ul>';
    $teachers= $db->get_class_teachers($cid);
    foreach ($teachers as $teacher) {
        $sca_name = $teacher['title'] . ' ' . $teacher['sca_first'] . ' ' . $teacher['sca_last'];
        $mundane_name = $teacher['PrefixName'] . ' ' . $teacher['first'] . ' ' . $teacher['last'];
        echo'
        <li><label class="bold">Instructor: </label> <a href="profile.php?id=' . $uid . '">' . $sca_name;
        if ($mundane_name != "  ") {
            echo' (' . $mundane_name . ')';
        }
        echo'</a></li>';
    }
    echo'<li><label class="bold">Schedule: </label> ';
    if ($room != 'n/a') {
        echo $room . ' on ' . $start_time.' to '.$end_time;
    }
    else {
        echo '(n/a)';
    }
    echo '</li>';
    if ($row['StyleID'] > 1) {echo '<li><label class="bold">Class Style: </label>'.$row['styleName'].'</li>';}
    //if ($row['AerobicID'] > 2) {echo '<li><label class="bold">Aerobic Level: </label>'.$row['aerobicName'].'</li>';}
    if ($row['DifficultyID'] > 1) {echo '<li><label class="bold">Suggested Difficulty Level: </label>'.$row['difficultyName'].'</li>';}
    echo'</ul>
    <p>' . $desc . '</p>
</div>
        ';
    }
    $result = $db->get_unscheduled_classes($kwds['KWID']);

    if (count($result) > 0) {
        echo '
<div class="printhide"><h2>Unscheduled or Canceled Classes</h2>
    <ul>';
        foreach ($result as $row) {
            echo '
        <li>' . $row['name'] . '</li>';
        }
        echo '
    </ul>';
    echo '</div>';
    }
}
include_once('includes/footer.php');
?>
