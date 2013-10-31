<?php
/*
 * KWDS Food
 */
require_once('includes/header.php');
$info = $db->get_kwds_field('food',$kwds['KWID']);

echo '<h1>Food</h1>';
if ($info=='') echo '<p>There is no information to report.</p>';
else echo $info;

include_once('includes/footer.php');
?>
