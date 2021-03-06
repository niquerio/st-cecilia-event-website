<?php
/*
 * St. Cecilia Schedule
 */
require_once('includes/header.php');

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<div class="box error">You must be logged in to view this page.</div>';
    redirect('login');
    include_once('includes/footer.php');
    die;
}
// If the "UPDATE CLASS" button was pressed, update the class info
if (isset($_POST['class'])) {

    $cid=$_POST['cid'];
    $name=sanit($_POST['name']);
    $desc=sanit($_POST['desc']);
    $fee=$_POST['fee'];
    //$hours=$_POST['hours']*60+$_POST['minutes'];
    $hours=50;
    $style=$_POST['style'];
    $type=$_POST['type'];
    $diff=$_POST['difficulty'];
    $notes=sanit($_POST['notes']);

    $time=($_POST['hour']<8)?$_POST['hour']+12:$_POST['hour'];
    
    $db->update_class_by_user($cid,$desc,$diff,$fee,$hours,$name,$style,$type,$notes);
}

$cid = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;

$result = $db->get_class((int)$cid);
if ( ( count($result) >= 1 AND $db->is_teacher($cid,$_SESSION['user_id']) ) OR is_super_user() ) {
    $class_name = ($result['ClassName']);
    $desc = redisplay($result['ClassDescription']);
    $length = $result['hours'];
    //$hour=intval($length/60);
    //$minute=$length%60;
    $diff = $result['DifficultyID'];
    //$aero = $result['AerobicID'];
    $era = $result['EraID'];
    $style = $result['StyleID'];
    $type = $result['TypeID'];
    $fee = $result['fee'];
    $hr = date('g', (strtotime($result['day'])));
    $min = date('i', (strtotime($result['day'])));
    $notes = redisplay($result['other']);
    $uid = $result['UserID'];
    $cdate= date('Y-m-d', (strtotime($result['day'])));
    $sca_name = $result['TitleName'] . ' ' . $result['SCAFirst'] . ' ' . $result['SCALast'];
    $mundane_name = $result['PrefixName'] . ' ' . $result['MundaneFirst'] . ' ' . $result['MundaneLast'];
?>
<form class="form" action="edit_class.php?kwds=<?php echo $kwds['KWID'] ?>&id=<?php echo $cid ?>" method="post">
<div class="class_info">
    <ul>


<?php $teachers=$db->get_class_teachers($cid); ?>
        <li id="current_class_teachers"><label for="teacher">Teacher(s):</label><?php
        foreach ($teachers as $teacher) {
            echo '<p id="current_class_teacher_'.$teacher['UserID'].'"><label></label>';
            echo '<a href="profile.php?id='.$teacher['UserID'].'">'.$teacher['sca_first'].' '.$teacher['sca_last'];
            if ($teacher['first']!="") {
                echo '('.$teacher['first'].' '.$teacher['last'].')';
            }
            echo '</a> [<a href="javascript:void()" onClick="remove_teacher_from_class(' .$cid. ',' .$teacher['UserID'].')">Remove</a>]</p>';
        }
        
    echo('<p id="show_hide_teacher_search"><a href="javascript:void()" onClick="search_for_teacher(\''.$cid.'\')">Add Teacher</a></p></li>')?>


        <li><label for="name">Class Name:</label><input type="text" name="name"<?php echo 'value="'.$class_name.'"'; ?> /></li>
        <li><label for="desc">Class Description:</label><textarea name="desc" cols="50" rows="10"><?php echo $desc ?></textarea></li>
        <li><label for="hours" style="padding:0px">Length of Class:</label> 50 Minutes<?php// dropdown_num('hours', 0, 8, 1,$hour); echo 'Hrs '; dropdown_num('minutes', 0, 55, 5, $minute); echo 'Minutes'; ?></li>
        <li><label for="difficulty">Suggested Skill Level:</label><?php $db=new db; $result=$db->get_list('difficulty'); dropdown($result, 'difficulty', $diff) ?></li>
        <li><label for="type">Type of Class:</label><?php $result=$db->get_list('type'); dropdown($result, 'type', $type) ?></li>
        <li><label for="style">Class Format:</label><?php $result=$db->get_list('style'); dropdown($result, 'style', $style) ?></li>
        <li><label for="fee">Class Fee:</label><input type="text" name="fee" <?php echo 'value="'.$fee.'"'; ?> /></li>
        <li><label for="notes">Special Notes:<br /></label><textarea name="notes" cols="50" rows="10"><?php echo $notes; ?></textarea>
            <img src="images/icons/information.png" style="vertical-align:top;" alt="Special Notes"
                 title="Enter information such as schedule preferences/avoidances, arrival/departure dates, special needs, etc."
                 onclick="alert('Enter information such as schedule preferences/avoidances, arrival/departure dates, special needs, etc.')" /></li>
        <li><label></label><input type="submit" class="button" name="class" value="Update Class" /></li>
    </ul>
    <input type="hidden" name="kwds" value="<?php echo $kwds['KWID'] ?>" />
    <input type="hidden" name="cid" value="<?php echo $cid ?>" />
</div>
</form>
<?php }
else {
    echo '<div class="error box">You do not have permissions to edit this class.</div>';
    redirect('index',$kwds['KWID']);
}
include_once('includes/footer.php');
?>
