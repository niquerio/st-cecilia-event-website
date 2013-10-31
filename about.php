<?php
/*
 * KWDS About or FAQ Pag
 */
require_once('includes/header.php');
$info = $db->get_kwds_field('faq',$kwds['KWID']);

echo '<h1>About and FAQ</h1>';
if ($info=='') echo '<p>There is no information to report.</p>';
else echo $info;

?>


<!--If user is KWDS staff, show list of questions -->


<!--Else show all questions and answers -->


<?php
include_once('includes/footer.php');
?>
