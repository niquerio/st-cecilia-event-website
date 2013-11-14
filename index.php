<?php
/*
 * KWDS Index Home Page
 */
require('includes/header.php');

?>
    <h1 class="header">
    The Barony of Cynnabar Presents<br>
        <a href="index.php?kwds=<?php echo $kwds['KWID']; ?>" title="Home">St. Cecilia at the Tower <? echo $kwds['KWID']; ?></a></h1>
    <div class="entry">
<img alt="st_cecilia_guido_mashup" src="http://cynnabar.org/sites/cynnabar.org/files/u44/cecilia_tower_no_text_small.png" style="border:0px solid;margin:10px;float:right;" />
<strong><meta http-equiv="content-type" content="text/html; charset=" utf-8""="" /><span style="font-weight: normal; ">
                    <strong>Date: </strong> <?php echo date('M j, Y', strtotime($kwds['start_date'])).' to '.date('M j, Y', strtotime($kwds['end_date'])); ?><br><br>
                    <strong>Location: </strong><?php echo $kwds['kwdsName']; ?><br><br>
            <strong>Address: </strong><?php echo $kwds['address']; ?>; <?php echo $kwds['city'];
                if ($kwds['city'] != "" && $kwds['state'] != "") {
                    echo ', ';
                }
                echo $kwds['state'].' '. $kwds['zip']; ?> 
<br><br>
            <?php echo redisplay($kwds['description']); ?>
                <br><br>
    </div>
    


<?php require('includes/footer.php'); ?>
