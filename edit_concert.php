<?php
/*
 * Concert and Dance Parties
 */
require_once('includes/header.php');

// Make sure the user is logged in as a user that can edit the concerts
if (!(can_add_rooms($_SESSION['user_id'],$kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds() OR is_super_user())) {
    echo 'div class="box error">You do not have permissions to view this page.</div>';
    redirect('index',$kwds['KWID']);
    include_once(includes/footer.php);
    die;
}
// If the "UPDATE CLASS" button was pressed, update the class info
if (isset($_POST['concert'])) {

    $concerts=$_POST['concerts'];
    $db->update_concerts($kwds['KWID'], $concerts);
}

$cid = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;
$concerts = $db->get_kwds_field('concerts',$kwds['KWID']);

?>
<h1>Concerts</h1>
<form class="form" action="edit_concert.php?kwds=<?php echo $kwds['KWID'] ?>&id=<?php echo $cid ?>" method="post">
<div class="class_info">
    <ul>
        <li><label for="concerts">Information:</label><textarea name="concerts" cols="50" rows="10"><?php echo $concerts ?></textarea></li>
        <li><label></label><input type="submit" class="button" name="concert" value="Update Concert Info" /></li>
    </ul>
    <input type="hidden" name="kwds" value="<?php echo $kwds['KWID'] ?>" />
    <input type="hidden" name="cid" value="<?php echo $cid ?>" />
</div>
</form>

<?php
include_once('includes/footer.php');
