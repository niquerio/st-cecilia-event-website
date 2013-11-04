<?php
require_once(dirname(__FILE__).'/../includes/init.php');
require_once(dirname(__FILE__).'/../includes/functions.php');

if(!post('cid') || !post('teacher_id')) exit;

$cid = sanit(post('cid'));
$teacher_id = sanit(post('teacher_id'));

$result=$db->insert_teacher($cid, $teacher_id);
print($result);
?>
