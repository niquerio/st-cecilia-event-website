<?php
/*
 * Cecilia Concerts 
 */
require_once('includes/header.php');

$info = $db->get_kwds_field('evening_activities',$kwds['KWID']);

echo '<h1>Evening Activities</h1>';
if ($info=='') echo '<p>There is no information to report.</p>';
else echo $info;

include_once('includes/footer.php');
?>
