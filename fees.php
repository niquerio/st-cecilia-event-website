<?php
/*
 * KWDS Site Fees
 *
 * in process of deleting and editing fees
 */
require_once('includes/header.php');

if ((is_autocrat($_SESSION['user_id'], $kwds['KWID']) AND $kwds['KWID']>=$db->get_next_kwds()) OR is_super_user()) {

// Deletes a fee
if (isset($_POST['delete'])) {
    $db->delete_fee($_POST['edit_num']);
    $_POST['edit_num']='';
}
// Updates a fee
if (isset($_POST['update'])) {
    $db->update_fee($_POST['desc'], $_POST['edit_num'], $_POST['name'], $_POST['prereg'], $_POST['price'], $_POST['type']);
    $_POST['edit_num']='';
    echo '<div class="box success">The fee has been updated.</div>';
}

$type=1;
if (isset($_POST['edit_num']) AND $_POST['edit_num']!='') {
    $result=$db->get_fee($_POST['edit_num']);
    if (count($result)>0) {
        $name = $result['name'];
        $desc = $result['description'];
        $price = $result['price'];
        $pre = $result['prereg'];
        $type = $result['fee_type_id'];
    }
}
if (isset($_POST[addfee])) {
    if (!isset($_POST['name']) OR $_POST['name']=="") {
        echo'<div class="warning box">You must enter a title for the fee.</div>';
    }
    elseif (floatval($_POST['price'])!=$_POST['price']) {
        echo '<div class="box warning">Please only enter a numeric value for the price.</div>';
    }
    else {
        $pre=(isset($_POST['prereg'])?$_POST['prereg']:0);
        $db->insert_fee($kwds['KWID'], $_POST['name'], $_POST['price'], $_POST['description'], $pre, $_POST['type']);
        echo '<div class="box success">The fee has successfully been added.</div>';
    }
}

echo '
<h1>Add/Edit Site Fees</h1>
<form action="fees.php?kwds='.$kwds['KWID'].'" method="post" class="form">
    <ul>
        <li><label>Enter Fee Title: </label><input type="textbox" name="name" value="'.$name.'" /></li>
        <li><label>Type of Fee: </label>';$result=$db->get_list('fee_type');dropdown($result, 'type', $type); echo '</li>
        <li><label>Price: </label><input type="textbox" name="price" value="'.$price.'" /></li>
        <li><label>Pre-registration Price: </label> <input type="checkbox" name="prereg" value="1"';
        if ($pre=='1') { echo ' checked="checked"'; }
        echo ' /></li>
        <li><label>Fee Description: <br />(optional)</label><textarea name="desc">'.$desc.'</textarea></li>';
        if (isset($_POST['edit_num']) AND $_POST['edit_num']!="") {
            echo '<li><input type="submit" class="button" name="delete" value="Delete" />
                <input type="submit" class="button" name="update" value="Edit Fee" />
                <input type="hidden" class="button" name="edit_num" value="'.$_POST['edit_num'].'" />';
        } 
        else {
            echo '<li><label></label><input type="submit" class="button" name="addfee" value="Add Fee" /></li>';
        }
    echo '</ul>';

$result=$db->get_fees($kwds['KWID']);

if (count($result) > 1) {
    foreach ($result as $row) {
        $desc = $row['description'];
        $fee = $row['FeeID'];
        $name = $row['FeeName'];
        $price = $row['price'];
        $type = $row['FeeTypeName'];
        if ($row['prereg']==0) {
            $pre="";
        }
        else {
            $pre="[Pre-registration Price]";
        }
        setlocale(LC_MONETARY, 'en_US');
        echo '<div class="box info"><input type="radio" name="edit_num" value="'.$fee.'" style="width:20px;" />'.$name.$pre.' - '.$type.' = '.money_format('%n', $price).' : '.$desc.
                /*'*/'</div>';
    }
    echo '<div><input type="submit" class="button" value="Edit" name="editfee" /></div>';
}
echo '</form>';

}
else {
    echo '<div class="box error">You do not have the proper documentation to view this page.</div>';
    redirect('index',$kwds['KWID']);
}
include_once('includes/footer.php');
?>

