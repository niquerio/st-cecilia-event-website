<?php
/*
 * KWDS Edit Site Info
 */
require_once('includes/header.php');
if (!(is_autocrat($_SESSION['user_id'], $kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds()) AND !is_super_user()) {
    echo '<div class="box error">You do not have permission to view this page.</div>';
    redirect('index',$kwds['KWID']);
    include_once('includes/footer.php');
    die;
}
$info = $db->get_kwds($kwds['KWID']);
if (count($kwds) > 0) {
    $address=isset($_POST['address'])?$_POST['address']:$kwds['address'];
    $attraction=isset($_POST['attraction'])?sanit($_POST['attraction']):redisplay($kwds['attractions']);
    $concerts=isset($_POST['concerts'])?$_POST['concerts']:$kwds['concerts'];
    $banner=isset($_POST['banner'])?$_POST['banner']:$kwds['banner'];
    $cday=isset($_POST['cday'])?$_POST['cday']:date('j', strtotime($kwds['class_date']));
    $city=isset($_POST['city'])?$_POST['city']:$kwds['city'];
    $cmonth=isset($_POST['cmonth'])?$_POST['cmonth']:date('n', strtotime($kwds['class_date']));
    $cyear=isset($_POST['cyear'])?$_POST['cyear']:date('Y', strtotime($kwds['class_date']));
    $country=isset($_POST['country'])?$_POST['country']:$kwds['country'];
    $desc=isset($_POST['desc'])?sanit($_POST['desc']):redisplay($kwds['description']);
    $dir=isset($_POST['dir'])?sanit($_POST['dir']):redisplay($kwds['directions']);
    $eday=isset($_POST['eday'])?$_POST['eday']:date('j', strtotime($kwds['end_date']));
    $emonth=isset($_POST['emonth'])?$_POST['emonth']:date('n', strtotime($kwds['end_date']));
    $eyear=isset($_POST['eyear'])?$_POST['eyear']:date('Y', strtotime($kwds['end_date']));
    $facebook=isset($_POST['facebook'])?$_POST['facebook']:$kwds['facebook'];
    $faq=isset($_POST['faq'])?$_POST['faq']:$kwds['faq'];
    $food=isset($_POST['food'])?sanit($_POST['food']):redisplay($kwds['food']);
    $group=isset($_POST['group'])?$_POST['group']:$kwds['group_id'];
    $kingdom=isset($_POST['kingdom'])?$_POST['kingdom']:$kwds['kingdom_id'];
    $linkDesc=isset($_POST['linkDesc'])?sanit($_POST['linkDesc']):redisplay($kwds['linkDesc']);
    $linkName=isset($_POST['linkName'])?sanit($_POST['linkName']):redisplay($kwds['linkName']);
    $linkUrl=isset($_POST['linkUrl'])?sanit($_POST['linkUrl']):redisplay($kwds['linkUrl']);
    $lodging=isset($_POST['lodging'])?sanit($_POST['lodging']):redisplay($kwds['lodging']);
    $merchant=isset($_POST['merchant'])?sanit($_POST['merchant']):redisplay($kwds['merchants']);
    $name=isset($_POST['name'])?$_POST['name']:$kwds['kwdsName'];
    $parking=isset($_POST['parking'])?sanit($_POST['parking']):redisplay($kwds['parking']);
    $proceeding=isset($_POST['proceeding'])?sanit($_POST['proceeding']):redisplay($kwds['proceedings']);
    $sday=isset($_POST['sday'])?$_POST['sday']:date('j', strtotime($kwds['start_date']));
    $smonth=isset($_POST['smonth'])?$_POST['smonth']:date('n', strtotime($kwds['start_date']));
    $state=isset($_POST['state'])?$_POST['state']:$kwds['state'];
    $status=isset($_POST['status'])?$_POST['status']:$kwds['status_id'];
    $syear=isset($_POST['syear'])?$_POST['syear']:date('Y', strtotime($kwds['start_date']));
    $zip=isset($_POST['zip'])?$_POST['zip']:$kwds['zip'];
}
if (isset($_POST['submit'])) {
    $class_date=$cyear.'-'.$cmonth.'-'.$cday.' 00:00:00';
    $start_date=$syear.'-'.$smonth.'-'.$sday.' 00:00:00';
    $end_date=$eyear.'-'.$emonth.'-'.$eday.' 00:00:00';
    $db->update_kwds($address,$attraction,$concerts,$banner,$city,$class_date,$country,$desc,$dir,$end_date,$facebook,$faq,$food,
            $group,$kingdom,$kwds['KWID'],$linkDesc,$linkName,$linkUrl,$lodging,$merchant,$name,$parking,
            $proceeding,$start_date,$state,$status,$zip);
    echo '<div class="box success">The KWDS information has been updated!</div>';
}
?>
<h1>Edit Site Information</h1>
<form class="form" action="kwds.php?kwds=<?php echo $kwds['KWID']?>" method="post">
    <h2>Location</h2>
    <ul>
        <li><label>Name of Site:</label><input name="name" type="text" <?php if(isset($name)) {echo 'value="'.$name.'"';} ?> /></li>
        <li><label>Address:</label><input name="address" type="text" <?php if(isset($address)) {echo 'value="'.$address.'"';} ?> /></li>
        <li><label>City:</label><input name="city" type="text" <?php if(isset($city)) {echo 'value="'.$city.'"';} ?> /></li>
        <li><label>State:</label><input name="state" type="text" <?php if(isset($state)) {echo 'value="'.$state.'"';} ?> /></li>
        <li><label>Zip Code:</label><input name="zip" type="text" <?php if(isset($zip)) {echo 'value="'.$zip.'"';} ?> /></li>
        <li><label>Country:</label><input name="country" type="text" <?php if(isset($country)) {echo 'value="'.$country.'"';} ?> /></li>
    </ul>
    <h2>Description (for Home page)</h2>
    <ul>
        <li><label>Description:</label><textarea name="desc" cols="40" rows="10"><?php if(isset($desc)) {echo redisplay($desc);} ?></textarea></li>
    </ul>
    <h2>Directions</h2>
    <ul>
        <li><label>Directions:</label><textarea name="dir" cols="40" rows="10"><?php if(isset($dir)) {echo redisplay($dir);} ?></textarea></li>
    </ul>
    <h2>Status</h2>
    <ul>
        <li><label>Status:</label><?php $result=$db->get_list('status'); dropdown($result, 'status', $status) ?></li>
        <li><label>Group:</label><?php $result=$db->get_list(DB_NAME.'.group');
            $index = (isset($_POST['group']))? $_POST['group']:$group;
            dropdown($result, "group", $index); ?></li>
        <li><label>Kingdom:</label><?php $result=$db->get_list('kingdom'); dropdown($result, 'kingdom',$kingdom) ?></li>
    </ul>
    <h2>Registration</h2>
    <ul>
        <li><label>Link Name:</label><input name="linkName" type="text" <?php if(isset($linkName)) {echo 'value="'.$linkName.'"';} ?> /></li>
        <li><label>URL Link:</label><input name="linkUrl" type="text" <?php if(isset($linkUrl)) {echo 'value="'.$linkUrl.'"';} ?> /></li>
        <li><label>Description:</label><textarea name="linkDesc" cols="40" rows="10"><?php if(isset($linkDesc)) {echo redisplay($linkDesc);} ?></textarea></li>
    </ul>
    <h2>Optional Links</h2>
    <ul>
        <li><label>Banner URL:</label><input name="banner" type="text" <?php if(isset($banner)) {echo 'value="'.$banner.'"';} ?> /></li>
        <li><label>Facebook URL:</label><input name="facebook" type="text" <?php if(isset($facebook)) {echo 'value="'.$facebook.'"';} ?> /></li>
    </ul>
    <h2>Concerts</h2>
    <ul>
        <li><label>Information:</label><textarea name="concerts" cols="40" rows="10"><?php if(isset($concerts)) {echo redisplay($concerts);} ?></textarea></li>
    </ul>
    <h2>Food</h2>
    <ul>
        <li><label>Information:</label><textarea name="food" cols="40" rows="10"><?php if(isset($food)) {echo redisplay($food);} ?></textarea></li>
    </ul>
    <h2>Local Attractions</h2>
    <ul>
        <li><label>Information:</label><textarea name="attraction" cols="40" rows="10"><?php if(isset($attraction)) {echo redisplay($attraction);} ?></textarea></li>
    </ul>
    <h2>Frequently Asked Questions</h2>
    <ul>
        <li><label>F.A.Q.:</label><textarea name="faq" cols="40" rows="10"><?php if(isset($faq)) {echo redisplay($faq);} ?></textarea></li>
    </ul>
    <h2>Lodging</h2>
    <ul>
        <li><label>Information:</label><textarea name="lodging" cols="40" rows="10"><?php if(isset($lodging)) {echo redisplay($lodging);} ?></textarea></li>
    </ul>
    <h2>Parking</h2>
    <ul>
        <li><label>Information:</label><textarea name="parking" cols="40" rows="10"><?php if(isset($parking)) {echo redisplay($parking);} ?></textarea></li>
    </ul>
    <h2>Proceedings</h2>
    <ul>
        <li><label>Information:</label><textarea name="proceeding" cols="40" rows="10"><?php if(isset($proceeding)) {echo redisplay($proceeding);} ?></textarea></li>
    </ul>
    <h2>Merchants</h2>
    <ul>
        <li><label>Information:</label><textarea name="merchant" cols="40" rows="10"><?php if(isset($merchant)) {echo redisplay($merchant);} ?></textarea></li>
    </ul>
    <h2>Dates</h2>
    <ul>
        <li><label>Class Cutoff Date:</label><?php dropdown_num('cmonth',1,12, 1,$cmonth);dropdown_num('cday', 1, 31,1,$cday); dropdown_num('cyear', 1997,date('Y',time())+5,1,$cyear) ?></li>
        <li><label>Start Date:</label><?php dropdown_num('smonth',1,12,1,$smonth);dropdown_num('sday', 1, 31,1,$sday); dropdown_num('syear', 1997,date('Y',time())+5,1, $syear) ?></li>
        <li><label>End Date:</label><?php dropdown_num('emonth',1,12,1,$emonth);dropdown_num('eday', 1, 31,1,$eday); dropdown_num('eyear', 1997,date('Y',time())+5,1,$eyear) ?></li>
        <li><label></label><input type="submit" name="submit" value="Update" /></li>
    </ul>

</form>
<?php
include_once('includes/footer.php');
?>
