<?php
ob_start();
require_once('init.php'); 
//require_once ('ChromePhp.php');
//ChromePHP::log($db->get_next_kwds());
//ChromePHP::log($kwds['KWID']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>St. Cecilia at the Tower</title>
        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!--<link rel="icon" type="image/png" href="http://kwds.org/images/icons/favicon.png"> -->
        <link rel="shortcut icon" type="image/x-icon" href="http://cynnabar.org/sites/default/files/cynnabar_favicon.ico">
        <link rel="stylesheet" href="css/kwds.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
        <link rel="stylesheet" href="jquery/css/pepper-grinder/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen" /> 
        <script type="text/javascript" src="jquery/js/jquery-1.9.1.js"></script> 
        <script type="text/javascript" src="jquery/js/jquery-ui-1.10.3.custom.min.js""></script> 
        <script type="text/javascript" src="js/functions.js"></script> 
    </head>
  <script>
  $(function() {
    $( document ).tooltip();
  });
  </script>
    <body>
        <div id="wrapper">
            <div id="header">
                <?php if ($kwds['banner'] == "") { ?>
                    <h1>
                        <!--<div class="float_left inline"><img src="./images/kingdoms/<?php// echo $kwds['arms']; ?>" alt="<?php// echo $kwds['kingdom']; ?>" title="<?php// echo $kwds['kingdom']; ?> Kingdom"/></div> -->
                        <a href="<?php echo $kwds['KWurl'] ?>">St. Cecilia at the Tower <?php echo roman($kwds['KWID']); ?></a>
                        <?php if($kwds['facebook'] != '') { ?><div class="float_right" style="margin-top:28px;margin-right:3px;"><a href="<?php echo $kwds['facebook']; ?>" target="_blank"><img src="images/icons/facebook.png" alt="St. Cecilia at the Tower Facebook Page" title="St. Cecilia at the Tower Facebook Page" style="border:0px;" /></a></div><?php } ?>
                    </h1>
                <?php if ($kwds['status_id']==4) {
                    echo '<h2>Open for Bids</h2>';
                } else { ?>
                    <h2><?php echo date('M j, Y', strtotime($kwds['start_date'])).' to '.date('M j, Y', strtotime($kwds['end_date'])); ?></h2>
                <?php } } else { ?>
                    <div id="banner" style="width:953px;height:156px;background:url(images/header/<?php echo $kwds['banner']; ?>) no-repeat;fload:left;">
                       <!-- <div class="inline"><img src="./images/kingdoms/<?php// echo $kwds['arms']; ?>" alt="Kingdom Arms" /></div> -->
                        <!--<a href="<?php// echo $kwds['KWurl'] ?>"><img width="850" height="150" src="images/header/<?php// echo $kwds['banner']; ?>" title="St. Cecilia <?php// echo $kwds['KWID']; ?>" /></a>-->
                        <?php if($kwds['facebook'] != '') { ?><div class="float_right" style="margin-top:90px;margin-right:3px;"><a href="<?php echo $kwds['facebook']; ?>" target="_blank"><img src="images/icons/facebook.png" alt="St. Cecilia at the Tower Facebook Page" title="St. Cecilia at the Tower Facebook Page" style="border:0px;" /></a></div><?php } ?>
                    </div>
                <?php } ?>
            </div>
            <div id="main_content">
                <?php
                require_once('includes/menu.php');
                require_once('includes/side_menu.php');
                ?>
                <div id="container">
<?php ob_flush() ?>
