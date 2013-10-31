<?php
/*
 * Add a Teacher to a Class
 */
require_once('includes/header.php');
$class = isset($_GET['id'])? $_GET['id']:0;

// Check to see if user created class or is teacher of class
if (is_teacher($class, $_SESSION['user_id'])==false AND is_super_user()==false) {
    echo '<div class="box error">You do not have permissions to view this page.</div>';
    redirect('index');
    include_once('includes/footer.php');
    die;
// If a user is selected, add him/her to the class
} elseif(isset($_POST['add'])) {
    $user = $_POST['add'];
    $result=$db->insert_teacher($class, $user);
    echo'<div class="success box">The user has been added as a teacher for the class.</div>';
    redirect('index');
    die;
} else {

    ?>
<form class="form" action="add_teacher.php?id=<?php echo $class ?>" method="post">
    <h2>Add a Teacher/Co-Teacher</h2>
    <h3>Class Name: "<?php echo $db->get_class_name($class)?>"</h3>
    <ul>
        <li><label>Search by Name: </label><input type="textbox" name="search" /></li>
        <li><label> </label><input class="button" type="submit" value="Search" /></li>
    </ul>

<?php
if (isset($_POST['search'])) {
    $search=$_POST['search'];
    $results=$db->get_user_list($search);
    if (count($results) > 0) {
        echo '<ul>';
        foreach ($results as $result) {
            echo '    <li><input class="radio" type="radio" name="add" value="'.$result['id'].'" />'
                .$result['first'].' '.$result['last'].' ('.$result['sca_first'].' '.$result['sca_last'].')';
        }
        echo '    <li><input class="button" type="submit" value="Add" /></li>';
    }
}
?>

</form>

<?php } ?>

<?php
include_once('includes/footer.php');
?>
