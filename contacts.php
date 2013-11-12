<?php
/*
 * St. Cecilia Contact Page
 */
require_once('includes/header.php');

$result=$db->get_staff($kwds['KWID']);
?>

<h1>St. Cecilia <?php echo $kwds['KWID'] ?> Staff Members</h1>
<?php
if (count($result) > 0) {
    foreach ($result as $row) {
        $uid=$row['UserID'];
        echo '<div class="staff"><h1>'.  $row['JobName'].'</h1>';
        echo '<p><label>Mundane Name: </label><a href="profile.php?id='.$uid.'">'.$row['MundaneFirst'].' '.$row['MundaneLast'].'</a></p>';
        echo '<p><label>SCA Name: </label><a href="profile.php?id='.$uid.'">'.display_HTML($row['SCAFirst']).' '.display_HTML($row['SCALast']).'</a></p>';
echo'
<p>
    <form action="email.php" method="post">
        <input type="hidden" name="uid" value="' . $uid . '" />
        <input type="submit" class="button float_right" value="Send an email" /><br />
    </form>
</p></div>';
    }
}
include_once('includes/footer.php');
