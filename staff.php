<?php
/*
 * St. Cecilia Add New Staff Page
 */
require_once('includes/header.php');
$staff = $db->get_staff($kwds['KWID']);
// If you're not autocrat(or co-autocrat), you can't view this page
if (!(is_autocrat($_SESSION['user_id'], $kwds['KWID']) AND $kwds['KWID'] >= $db->get_next_kwds()) AND !is_super_user()
        AND (!is_autocrat($_SESSION['user_id'], ($kwds['KWID']-1)) OR count($staff)!=0)) {
    echo '<div class="box error">You do not have permission to view this page.</div>';
    redirect('index.php');
    die;
}
$kwds['KWID']=(isset($_POST['kwds']) AND $_POST['kwds']!="")? $_POST['kwds']:$kwds['KWID'];

// If the UPDATE button was pressed, do this
if (isset($_POST['update'])){
    if (isset($_POST['updated'])) {
        $db->update_role($_POST['updated'], $_POST['user'], $_POST['job'], $kwds['KWID']);
        echo '<div class="box success">Your staff has been updated!</div>';
    }
    else {
        $result=$db->get_role($_POST['role']);
        $job=  $result[0]['JobID'];
        $user=  $result[0]['UserID'];
    ?>
<form class="form" action="staff.php?kwds=<?php echo $kwds['KWID'] ?>" method="post">
    <h1> Update User and Role</h1>
    <ul>
        <li><label>Job: </label>
            <?php
                $result = $db->get_list('job');
                dropdown($result, 'job', $job);
            ?>
        </li>
        <li><label>Person: </label>
            <?php
                $result = $db->get_users_list();
                dropdown($result, 'user', $user);
            ?>
        </li>
<?php
if  ($_SESSION['user_id']==1) {
?>
        <li><label> St. Cecilia #: </label><input type="text" name="kwds" /></li>
<?php
}
?>
        <li><label></label><input class="button" name="update" type="submit" value="Update Staff" /></li>
    </ul>
    <input type="hidden" name="updated" value="<?php echo $_POST['role'] ?>" />
</form>
<?php
    include_once('includes/footer.php');
    die;
    }
}
// If the DELETE button was pressed, do this
elseif (isset($_POST['delete'])) {
    if (isset($_POST['deleted'])) {
        $result=$db->delete_role($_POST['deleted']);
        echo '<div class="box success">The staff member was removed!</div>';
    }
    else {
    $result= $db->get_role($_POST['role']);
    if ($result['JobID']==1 AND !is_super_user()){
        echo '<div class="box error">You can not remove the autocrat.</div>';
        redirect('staff');
        die;
    }
    echo '
<form action="staff.php?kwds='.$kwds['KWID'].'" class="form" method="post">
    <p>Are you sure you want to remove '.  $result[0]['username'].' as '.  $result[0]['JobName'].'?</p>
    <input class="button" type="submit" name="delete" value="Yes" /><input class="button" type="submit" value="No" />
    <input type="hidden" name="deleted" value="'.$_POST['role'].'" />
</form>';
    include_once('includes/footer.php');
    die;
    }
}
// If the ADD button was pressed, do this
elseif (isset($_POST['add'])) {
    $db->insert_role($kwds['KWID'], $_POST['user'], $_POST['job']);
    echo '<div class="box success">A new member was added to your staff!</div>';
    if (is_autocrat($_SESSION['user_id'], ($kwds['KWID']-1)) AND count($staff)==0) {
        echo $kwds['KWID']+1;
        $next = $db->get_kwds($kwds['KWID']+1);
        if ($next['KWID']!=$kwds['KWID']+1) {
            $db->insert_kwds($kwds['KWID']+1);
        }
        redirect('index');
        die;
    }
}
?>
<form class="form" action="staff.php?kwds=<?php echo $kwds['KWID'] ?>" method="post">
    <?php if (is_autocrat($_SESSION['user_id'], ($kwds['KWID']-1)) AND count($staff)==0) { ?>
    <h1>Add Next Event Steward</h1>
    <ul>
        <li><input type="hidden" name="job" value ="1" /></li>
        <li><label>Choose Person:</label><?php $result = $db->get_users_list(); dropdown($result, 'user', 0)?></li>
        <li><label></label><input class="button" name="add" type="submit" value="Add Event Steward" /></li>
    </ul>
</form>
<?php    }
    else { ?>
    <h1>Add a New Staff Member</h1>
    <ul>
        <li><label>Choose Job: </label>
            <?php
                $result = $db->get_list('job');
                dropdown($result, 'job');
            ?>
        </li>
        <li><label>Choose Person: </label>
            <?php
                $result = $db->get_users_list();
                dropdown($result, 'user', 0);
            ?>
        </li>
<?php
if  ($_SESSION['user_id']==1) {
?>
        <li><label> St. Cecilia #: </label><input type="text" name="kwds" /></li>
<?php
}
?>
        <li><label></label><input class="button" name="add" type="submit" value="Add Staff" /></li>
    </ul>

    <h1>Edit or Delete Existing Staff Members</h1>
    <?php
    $jobs = $db->get_staff($kwds['KWID']);
?>
    <ul>
        <li><label>Choose Staff Member:</label><?php dropdown($jobs, 'role')?></li>
        <li><input class="button" name="delete" type="submit" value="Delete" />
            <input class="button" name="update" type="submit" value="Update Staff" /></li>
    </ul>
    </form>
<?php }
include_once('includes/footer.php');
?>
