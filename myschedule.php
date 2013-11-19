<?php
/*
 * St. Cecilia My Schedule
 */
require_once('includes/header.php');
?>
<h1>My Schedule for St. Cecilia <?php echo $kwds['KWID'] ?></h1>
<?php
if (!isset($_POST['checkboxes'])) {
    echo '<div class="box attention">You don\'t have any classes selected from the schedule.</div>';
    redirect('schedule');
    include_once('includes/footer.php');
    die;
}
$where="class.id=-1 ";
echo '<form action="schedule.php?kwds='.$kwds['KWID'].'" method="post">';
foreach ($_POST as $key => $value) {
    echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
    if ($key!="checkboxes" AND $key!="europ" AND $key!="meast" AND $key!="music") {
        $where.="OR class.id=$value ";
    }
}
$results=$db->get_my_schedule($where);

if (count($results)<1) {
    echo '<div class="box attention">You don\'t have any classes selected from the schedule.</div>';
    redirect('schedule');
    include_once('includes/footer.php');
    die;
}
$day="";
echo '<div class="my_sched">';
foreach ($results as $result) {
    if ($day!=date('l',strtotime($result['day']))) {
        $day=date('l',strtotime($result['day']));
        echo "<br /><h2>$day</h2>";
    }
    echo '<div class="time">'.  date('g:i A',strtotime($result['day'])).'</div>';
    echo '<div class="room">'.$result['RoomName'].'</div>';
    echo '<div class="name">'.$result['ClassName'].'</div>';
    echo '<div class="prof">'.$result['Prof'].'</div>';
}
echo '</div>';
echo '<input class="button" name="checkboxes" type="submit" value="Return to Schedule" />';
echo '</form>';
include_once('includes/footer.php');
?>
