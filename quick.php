<?php
/*
 * KWDS Quick Login
 */
require_once('includes/header.php');

if (!is_super_user()) {
    echo '<div class="box error">You do not have permissions to view this page.</div>';
    redirect('index',$kwds['KWID']);
    include_once('includes/footer.php');
    die;
} elseif(is_super_user() AND isset($_POST['login'])) {
    $_SESSION['user_id']=$_POST['login'];
    echo'<div class="success box">You have changed accounts.</div>';
    redirect(index.php);
    die;
} else {

    ?>
<form class="form" action="quick.php" method="post">
    <ul>
        <li><label>Search: </label><input type="textbox" name="search" /></li>
        <li><label> </label><input class="button" type="submit" value="Search" /></li>
    </ul>

<?php
if (isset($_POST['search'])) {
    $search=$_POST['search'];
    $results=$db->get_user_list($search);
    if (count($results) > 0) {
        echo '<ul>';
        foreach ($results as $result) {
            echo '    <li><input class="radio" type="radio" name="login" value="'.$result['id'].'" />'
                .$result['first'].' '.$result['last'].' ('.$result['sca_first'].' '.$result['sca_last'].')';
        }
        echo '    <li><input class="button" type="submit" value="Login" /></li>';
    }
}
?>

</form>

<?php } ?>

<?php
include_once('includes/footer.php');
?>
