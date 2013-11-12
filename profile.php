<?php
/*
 * St. Cecilia User Profile Page
 */
require_once('includes/header.php');

// Retrieve the user id from the web address, otherwise set it to the user's id
$id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0;
$id = (isset($_GET['id'])) ? $_GET['id'] : $id;

$result = $db->get_user_info($id);
if (count($result) > 0) {
    $first = $result['MundaneFirst'];
    $last = $result['MundaneLast'];
    $nick = $result['nickname'];
    $sfirst = $result['SCAFirst'];
    $slast = $result['SCALast'];
    $pre = $result['PrefixName'];
    $email = $result['email'];
    $group = $result['GroupName'];
    $gurl = $result['GroupUrl'];
    $kingdom = $result['KingdomName'];
    $kurl = $result['kingdom.url'];
    $about = $result['about'];
    $title = $result['TitleName'];
?>
    <h1>User Profile
    <?php
    if ($id == $_SESSION['user_id']) { ?>
        <form action="user.php" method="post">
            <input type="hidden" name="uid" value="<?php echo $_SESSION['user_id'] ?>" />
            <a><input type="submit" name="edit_user" class="button edit" value="Edit Profile" /></a>
        </form>
        <?php
    }
    ?>
</h1>
<div class="profile">
    <ul>
        <li><label for="name">Mundane Name: </label><?php echo $pre . " " . $first . " " . $last; ?></li>
        <li><label>SCA Name: </label> <?php echo $title . " " . $sfirst . " " . $slast ?></li>
        <li><label>Group: </label> <a href="<?php echo $gurl ?>"><?php echo $group ?></a></li>
        <li><label>Kingdom: </label> <a href="<?php echo $kurl ?>"><?php echo $kingdom ?></a></li>
        <li>
            <form action="email.php" method="post">
                <input type="hidden" name="uid" value="<?php echo $id ?>" />
                <input type="submit" class="button float_right" value="Send an email" />
            </form>
        </li>
    </ul>
    <div class="bio">
        <h3>Biography:</h3>
        <p><?php echo $about; ?></p>
    </div>
    <?php
        $results=$db->get_user_jobs($id);
        if (count($results) >0) {
            echo '
    <div class="jobs">
        <h2>St. Cecilia Staff</h2>
        <ul>';
            
            foreach ($results as $result) {
                echo '
            <li><label>St. Cecilia '.$result['KWID'].': </label> '.$result['JobName'].'</li>';
            }
            echo '
        </ul>
    </div>';
        }
        $results=$db->get_user_classes($id);
        if (count($results)>0) {
            echo '
    <div class="classes">
        <h2>Classes Taught</h2>
        <ul>';

            foreach ($results as $result) {
                echo'
            <li>';
                if ($result['RoomID'] == 0 and $id == $_SESSION['user_id'] and $result['KWID']>= $db->get_next_kwds()) {
                    echo'(<a href="edit_class.php?kwds='.$result['KWID'].'&id='.$result['ClassID'].'">Edit</a> |
                        <a href="add_teacher.php?kwds='.$result['KWID'].'&id='.$result['ClassID'].'">Add Teacher</a>)';
                }
                if ($result['accepted'] == 1 or $id == $_SESSION['user_id']) {
                echo '<a href="schedule.php?kwds='. $result['KWID'].'&id='. $result['ClassID'].'">
                    <span class="bold">St. Cecilia '.$result['KWID'].'</span>: '.$result['ClassName'].'</a></li>';
                }
            }
            echo '
        </ul>
    </div>';
        }
        $results=$db->get_user_submissions($id);
        if (count($results)>0 and $id==$_SESSION['user_id']) {
            echo '
    <div class="classes">
        <h2>Classes Submitted</h2>
        <ul>';

            foreach ($results as $result) {
                echo'
            <li>';
                if ($result['RoomID'] == 0 and $id == $_SESSION['user_id']) {
                    echo'(<a href="edit_class.php?kwds='.$result['KWID'].'&id='.$result['ClassID'].'">Edit</a> |
                        <a href="add_teacher.php?kwds='.$result['KWID'].'&id='.$result['ClassID'].'">Add Teacher</a>)';
                }
                if ($result['accepted'] == 1 or $id == $_SESSION['user_id']) {
                echo '<a href="schedule.php?kwds='. $result['KWID'].'&id='. $result['ClassID'].'">
                    <span class="bold">St. Cecilia '.$result['KWID'].'</span>: '.$result['ClassName'].'</a></li>';
                }
            }
            echo '
        </ul>
    </div>';


        }
    ?>
</div>
<?php
} else {
    echo '<div class="box error">The user profile you are trying to view does not exist.</div>';
    redirect('index',$kwds['KWID']);
}

//user.id, user.first, user.last, sca_first, sca_last, title.name, prefix.name, nickname, email, group.name, kingdom.name, about
include_once('includes/footer.php');
?>
