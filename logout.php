<?php
/*
 * KWDS Logout Page
 */
//require_once('includes/header.php');
// $session = new Session;
//$session->logout();
ob_start();
require_once('includes/header.php');
$session->logout();
ob_flush();
echo '<div class="box success">You have successfully logged out!</div>';
redirect('index');
?>
