<?php
/*
 * KWDS Registration
 */
require_once('includes/header.php');
?>
<h1>Site Fees and Registration</h1>
<div class="fees">
<?php
$results = $db->get_fees($kwds['KWID']);
$register = $db->get_registration($kwds['KWID']);

if ($register['linkUrl']!='') {
    echo'<p><a href="'.$register['linkUrl'].'">'.$register['linkName'].' </a></p>
        <p>'.display_HTML($register['linkDesc']).'</p>';

}
$type="";
if (count($results) >= 1) {
    foreach ($results as $result) {
        if (($type.$prereg) != ($result['FeeTypeName'].$result['prereg'])) {
            $type = $result['FeeTypeName'];
            $prereg = $result['prereg'];
            echo '<h2>'.$type;
            if ($prereg==1) {
                echo ' Pre-registration Fees';
            }
            echo '</h2>';
        }
        $name = $result['FeeName'];
        $price = $result['price'];
        $desc = $result['description'];
        
        setlocale(LC_MONETARY, 'en_US');
        echo '<div class="box info"><span class="float_right">'. money_format('%n', $price) .'</span>
            <h3 class="bold">' . $name .'</h3><br />' . $desc .'</div>';
    }
}
else {
    echo '<p>There is no information to report.</p>';
}
?>
</div>
<?php
include_once('includes/footer.php');
?>
