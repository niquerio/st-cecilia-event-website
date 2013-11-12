<?php
/**
 * System Initialization File
 * 
 * Initializes subsystems and sets up some default variables for use across all
 * pages.
 */
//ob_start(); //TODO: DELETE ME AFTER SEPARATING BUSINESS AND PRESENTATIONAL LOGIC!
//Debugger
include 'ChromePhp.php';
// Include and setup the database
ChromePHP::log("hi!");
require(dirname(__FILE__).'/db.class.php');
ChromePHP::log("hi!");
$db=new db();
ChromePHP::log($db->get_charset());

// Include extra functions and start the session
require('functions.php');
require('session.php');
$session = new Session;

 //Determine which KWDS to display
 if (!isset($_GET['kwds']) OR !is_numeric($_GET['kwds'])) {
     $_GET['kwds']=2;
 }
// Get KWDS information and store it in variables
$kwds = $db->get_kwds($_GET['kwds']);
$kwds['KWurl'] = ($kwds['KWurl']!='')?$kwds['KWurl']:'index.php?kwds='.$kwds['KWID'];

?>
