<?php
/*
 * Attendance
 */
include_once('includes/header.php');

// If user is not logged in, send him to the login page
if (!isset($_SESSION['user_id'])) {
    echo '<div class="box error">You must be logged in to view this page.';
    redirect('login');
    die;
}
if (isset($_POST['update']) AND $_POST['attend']!=1) {
    $db->detele_attendance($_SESSION['user_id']);
    echo '<div class="box success">You are choosing not to attend this St. Cecilia.</div>';
}

$attendance = $db->get_attendance($_SESSION['user_id'],$kwds['KWID']);
$adate = (isset($attendance['arrival']))? date('Y-m-d',strtotime($attendance['arrival'])):'';
$adate = (isset($_POST['adate']))? $_POST['adate']: $adate;
$ddate = (isset($attendance['departure']))?date('Y-m-d',strtotime($attendance['departure'])): '';
$ddate = (isset($_POST['ddate']))?$_POST['ddate']: $ddate;
$ahr = (isset($attendance['arrival']))?date('g',strtotime($attendance['arrival'])): 12;
$ahr = (isset($_POST['ahour']))?$_POST['ahour']: $ahr;
$dhr = (isset($attendance['departure']))?date('g',strtotime($attendance['departure'])): 11;
$dhr = (isset($_POST['dhour']))?$_POST['dhour']: $dhr;
$amin = (isset($attendance['arrival']))?date('i',strtotime($attendance['arrival']))+0: 0;
$amin = (isset($_POST['aminute']))?$_POST['aminute']: $amin;
$dmin = (isset($attendance['departure']))?date('i',strtotime($attendance['departure']))+0: 55;
$dmin = (isset($_POST['dminute']))?$_POST['dminute']: $dmin;
$aPM = (isset($attendance['arrival']))?date('A',strtotime($attendance['arrival'])): 'AM';
$aPM = (isset($_POST['aPM']))?$_POST['aPM']: $aPM;
$dPM = (isset($attendance['departure']))?date('A',strtotime($attendance['departure'])): 'PM';
echo date('A',strtotime($attendance['arrival']));
$dPM = (isset($_POST['dPM']))?$_POST['dPM']: $dPM;
$attend = (isset($_POST['attend']))?$_POST['attend']:1;
if ($_POST['aPM']==1 AND $_POST['ahour'] != 12) { $_POST['ahour']=$_POST['ahour']+12;}
if ($_POST['dPM']==1 AND $_POST['dhour'] != 12) { $_POST['dhour']=$_POST['dhour']+12;}
$begin = date('Y-m-d H:i:s',mktime($_POST['ahour'],$_POST['aminute'],0,date('m',strtotime($_POST['adate'])),date('d',strtotime($_POST['adate'])),date('Y',strtotime($_POST['adate']))));
$end = date('Y-m-d H:i:s',mktime($_POST['dhour'],$_POST['dminute'],0,date('m',strtotime($_POST['ddate'])),date('d',strtotime($_POST['ddate'])),date('Y',strtotime($_POST['ddate']))));

if ($begin > $end and $attend==1) {
    echo '<div class="box error">Only the doctor can leave before he arrives.</div>';
}
elseif (isset($_POST['update']) AND $attend==1) {
    $db->update_attendance($_SESSION['user_id'], $kwds['KWID'],$begin, $end);
    echo '<div class="box success">Your arrival and departure times have been updated.</div>';
}

?>
<h1>Attendance Record</h1>
<form action="attendance.php?id=<?php echo $kwds['KWID']; ?>" method="post" class="form">
    <ul>
        <li>Will you be attending St. Cecilia <?php echo roman($kwds['KWID']);?> ?&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" value="1" name="attend" <?php if($attend==1) {echo 'checked="checked"';} ?> /> Yes
            &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" value="0" name="attend" <?php if($attend!=1) {echo 'checked="checked"';} ?> /> No</li>
        <br /><div class="box warning">If you are teaching any classes, enter your expected arrival and departure dates. <br />This will help in the scheduling of your classes.</div><br />
        <li><label>Arrival Date:</label><?php get_event_dates($adate,'adate'); /*name="date"*/ ?></li>
        <li><label>Arrival Time:</label><?php dropdown_num('ahour', 1, 12, 1,$ahr); echo ' : '; dropdown_num('aminute', 0, 55, 5, $amin); ?>
            &nbsp;&nbsp;<select name="aPM" style="width:60px;"><option value="AM">AM</option><option value="PM" <?php if ($aPM=="PM") {echo 'selected=selected';} ?>>PM</option></select></li>
        <li><label>Departure Date:</label><?php get_event_dates($ddate,'ddate'); /*name="date"*/ ?></li>
        <li><label>Departure Time:</label><?php dropdown_num('dhour', 1, 12, 1,$dhr); echo ' : '; dropdown_num('dminute', 0, 55, 5, $dmin); ?>
            &nbsp;&nbsp;<select name="dPM" style="width:60px;"><option value="AM">AM</option><option value="PM" <?php if ($dPM=="PM" or !isset($dPM)) {echo 'selected=selected';} ?>>PM</option></select></li>
        <li><label></label><input type="submit" class="button" name="update" value="Update" />
    </ul>
</form>

<?php
include_once('includes/footer.php');
?>
