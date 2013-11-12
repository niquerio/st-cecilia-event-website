<?php
/*
 * Concert and Dance Parties
 */
require_once('includes/header.php');

// Make sure the user is logged in as a user that can edit the evening_activities
if (!(can_add_rooms($_SESSION['user_id'],$kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds() OR is_super_user())) {
    echo 'div class="box error">You do not have permissions to view this page.</div>';
    redirect('index',$kwds['KWID']);
    include_once(includes/footer.php);
    die;
}
// If the "UPDATE CLASS" button was pressed, update the class info
if (isset($_POST['evening_activity'])) {

    $evening_activities=$_POST['evening_activities'];
    $db->update_evening_activities($kwds['KWID'], $evening_activities);
}

$cid = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;
$evening_activities = $db->get_kwds_field('evening_activities',$kwds['KWID']);

?>
<h1>Edit Evening Activities Information</h1>
<form class="form" action="edit_evening_activities.php?kwds=<?php echo $kwds['KWID'] ?>&id=<?php echo $cid ?>" method="post">
<div class="class_info">
    <ul>
        <li><label for="evening_activities">Information:</label><textarea name="evening_activities" cols="50" rows="10"><?php echo $evening_activities ?></textarea></li>
        <li><label></label><input type="submit" class="button" name="evening_activity" value="Update Evening Activity Info" /></li>
    </ul>
    <input type="hidden" name="kwds" value="<?php echo $kwds['KWID'] ?>" />
    <input type="hidden" name="cid" value="<?php echo $cid ?>" />
</div>
</form>

<?php
include_once('includes/footer.php');
