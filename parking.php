<?php
/*
 * KWDS Parking
 */
require_once('includes/header.php');
$info = $db->get_kwds_field('parking',$kwds['KWID']);

echo '<h1>Parking</h1>';
if ($info=='') echo '<p>There is no information to report.</p>';
else echo $info;

include_once('includes/footer.php');
?>
