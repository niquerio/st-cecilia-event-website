<?php
/*
 * St. Cecilia Adding a Room
 */
require_once('includes/header.php');

if (isset($_SESSION['user_id']) AND can_add_rooms($_SESSION['user_id'], $kwds['KWID'])) {
    if (isset($_POST['delete'])) {
        $db->delete_room($_POST['rooms'], $kwds['KWID']);
        echo '<div class="box success">The room you selected has be deleted.</div>';
    }
    else if (isset($_POST['updateRoom'])) {
        $name=$_POST['name'];
        $building = $_POST['building'];
        $floor = $_POST['floor'];
        $size = $_POST['size'];
        $notes = $_POST['notes'];
        $roomid = $_POST['rooms'];
        $db->update_room($building, $floor, $name, $notes, $roomid, $size);
        echo '<div class="box success">The room has been updated!</div>';
    }
    else if (isset($_POST['name'])) {
        $db->insert_room($_POST['name'], $_POST['building'], $_POST['size'], $_POST['kwds'], $_POST['notes']);
        echo '<div class="box success">The room has been successfully added!</div>';
    }

    $rooms=$db->get_rooms($kwds['KWID']);

?>
<?php
$roomid = 0;
$floor = 1;
if (isset($_POST['edit']) and isset($_POST['rooms'])) {
    echo '<h1>Edit Room</h1>';
    $result = $db->get_room($_POST['rooms']);
    $name=$result['name'];
    $building = $result['building'];
    $floor = $result['floor'];
    $size = $result['size'];
    $notes = $result['note'];
    $roomid = $result['id'];
} else {
    echo '<h1>Add A Room</h1>';
}

?>
<form class="form" action="room.php" method="post">
    <ul>
        <li><label>Room Name:</label> <input type="textbox" name="name" value="<?php echo $name ?>" /></li>
        <li><label>Building (optional):</label> <input type="textbox" name="building" value="<?php echo $building ?>"/></li>
        <li><label>Floor:</label> <?php dropdown_num('floor', 0, 10, 1, $floor);?></li
        <li><label>Size:</label> <input type="textbox" name="size" value ="<?php echo $size ?>"/></li>
        <li><label>Notes:</label> <textarea name="notes" cols="50" rows="5"><?php echo $notes ?></textarea></li>
        <?php if (is_super_user($_SESSION['user_id'])) {
            echo'
        <li><label>St. Cecilia:</label> <input type="number" name="kwds" value="'.$kwds['KWID'].'" />';
        } else {
            echo '<input type="hidden" name="kwds" value="'.$kwds['KWID'].'" />';
        } ?>
        <li><label></label> 
            <?php
            if (isset($_POST['edit']) and isset($_POST['rooms'])) {
                echo '<input type="hidden" name="rooms" value="'.$roomid.'" />';
                echo '<input type="submit" name="updateRoom" class="button" value="Update Room" /></li>';
            }
            else {
            echo '<input type="submit" name="addRoom" class="button" value="Add Room" /></li>';
            } ?>
    </ul>
</form>

<h1>List of Classrooms</h1>
<form class="form" action="room.php" method="post">
    <ul>
    <?php foreach ($rooms as $room) {
       echo'<li><input class="radio" type="radio" name="rooms" value="'.$room['id'].'" /><b>'.$room['name'].'</b>';
       if ($room[building]!= NULL) {
           echo' ['.$room['building'].']';
       }
       if ($room['note'] != NULL) {
           echo ' - '.$room['note'].'</li>';
       }
    }
    ?>
        <li><input type="submit" class="button" name="edit" value="Edit Selected Room" /><input type="submit" class="button" name="delete" value="DELETE Selected Room" /></li>
    </ul>
</form>

<?php
} else {
    echo '<div class="box error">You do not have permission to view this page.</div>';
    redirect('index',$kwds['KWID']);
}
include_once('includes/footer.php');
?>
