<?php
require_once(dirname(__FILE__).'/../includes/init.php');
require_once(dirname(__FILE__).'/../includes/functions.php');

$users=$db->get_users();
$result = array();
foreach ($users as $user){
    $current = array("label"=> "$user[SCAFirst] $user[SCALast] ($user[MundaneFirst] $user[MundaneLast])",
     "id" => $user['UserID']);
    //print_r($current);
    array_push($result, $current);
    //echo("$user[MundaneFirst] $user[MundaneLast] ($user[SCAFirst] $user[SCALast])\n");
}
$json = json_encode($result);
print $json;
?>
