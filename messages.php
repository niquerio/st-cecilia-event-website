<?php
/*
 * Messages
 */
include_once('includes/header.php');
if (!is_autocrat($_SESSION['user'],$kwds['KWID']) AND !is_class_scheduler($_SESSION['user_id'], $kwds['KWID'])) {
    echo '<div class="box error">You do not have permissions to view this page.</div>';
    redirect('index', $kwds['KWID']);
    die;
}

if (isset($_POST['select']) AND (is_autocrat($_SESSION['user'],$kwds['KWID']) 
        OR is_class_scheduler($_SESSION['user_id'], $kwds['KWID'])) ) {
    $euro = isset($_POST['european'])? 1:0;
    $middle = isset($_POST['middle'])? 1:0;
    $music = isset($_POST['music'])? 1:0;
    $other = isset($_POST['other'])? 1:0;
    $emails = $db->get_emails($euro, $middle, $music, $other, $kwds['KWID']);
    if (count($emails)==0) {
        echo '<div class="box error">There are no recipients.</div>';
    }
    else {$headers = '';
        foreach ($emails as $email) {
            if ($headers=='') {$headers=$email['email'];}
            else {$headers= ($email['email'].', '.$headers);}
        }
        $e_mail = $db->get_user_email($_SESSION['user_id']);
        mail('', '[St. Cecilia]'.$_POST['subject'], $_POST['message'], 'From: '.$e_mail."\r\n".'Bcc: '.$headers);
        mail($email, '[St. Cecilia]'.$_POST['subject'], $_POST['message'], 'From: '.$e_mail."\r\n");
        echo '<div class="box success">Your email was sent to '.count($emails).' people.</div><br />';
    }
}
?>

<h1>Send a Message</h1>
<form action="messages.php?kwds=<?php echo $kwds['KWID']; ?>" method="post" class="form">
    <ul>
        <li><label>Choose Recipients:</label></li>
        <li><label> </label><input type="checkbox" style="width:20px" name="european" />European Dance Teachers</li>
        <li><label> </label><input type="checkbox" style="width:20px" name="middle" />Middle Eastern Dance Teachers</li>
        <li><label> </label><input type="checkbox" style="width:20px" name="music" />Music Teachers</li>
        <li><label> </label><input type="checkbox" style="width:20px" name="other" />Other Teachers</li>
        <li><label>Subject:</label><input type="text" name="subject" /></li>
        <li><label>Message:</label><textarea cols="50" rows="8" name="message"></textarea></li>
        <li><label></label><input type="submit" name="select" value="Send Email" class="button" /></li>
    </ul>


</form>
<?php
include_once('includes/footer.php');
?>
