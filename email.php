<?php
/*
 * St. Cecilia Sending an Email
 * password: 18d6769919266cd0bd6cd78aa405d5d0
 */
require_once('includes/header.php');

/*if (!isset($_SESSION['user_id'])) {
    echo '<div class="box error">You must be logged in to send an email to another user.</div>';
    redirect('login');
}*/
if (isset($_POST['from']) AND check_email_address($_POST['from'])==false) {
    echo '<div class="box error">You need to enter a valid email address.</div>';
}
elseif (isset($_POST['code']) AND sanit(md5(strtolower($_POST['code'])))!="18d6769919266cd0bd6cd78aa405d5d0") {
    echo '<div class="box error">You did not answer the verification question correctly. (Hint: The answer is the topic of the event)</div>';
}
elseif (isset($_POST['message'])) {
    $to = $db->get_user_email($_POST['uid']);
    if (isset($SESSION['user_id'])) {
        $from = $db->get_user_email($_SESSION['user_id']);
    }
    else {
        $from = $_POST['from'];
    }
    $message = 'This message was sent via cynnabar.org:

'.escape_query($_POST['message']);
    mail($to, '[St. Cecilia]'.$_POST['subject'], redisplay($message), 'FROM: '.$from);
    mail($from, '[St. Cecilia]'.$_POST['subject'], redisplay($message).'(*You sent this message to '.$db->get_username($_POST['uid']).'*)', 'FROM: '.$from);
    echo '<div class="box success">Your email has been sent!</div>';
    redirect('index');
    die;
}
elseif (!isset($_POST['uid'])) {
    echo '<div class="box error">To send an email, go to the user\'s profile page</div>';
    redirect('index');
    die;
}
?>
<h1>Send an Email</h1>
<?php if (isset($_SESSION['user_id'])) { ?>
<div class="box attention">When sending an email to another user, your email will be sent so the user can reply directly you.</div>
<?php } ?>
<form action="email.php" method="post" class="form">
    <ul>
        <li><label>To:</label><input type="text" readonly="readonly" value="<?php echo $db->get_username($_POST['uid']) ?>" /></li>
        <?php if (!isset($_SESSION['user_id'])) { ?>
        <li><label>From:<br />(Enter your email)</label><input type="text" name="from" /></li>
        <?php } ?>
        <li><label>Subject:</label><input type="text" name="subject" /></li>
        <li><label>Message:</label><textarea name="message" cols="50" rows="5"></textarea> </li>
        <?php if (!isset($_SESSION['user_id'])) { ?>
        <li>Please answer the following question to verify your existence.</li>
        <li>St. Ceilia? is the patron saint of what?</li>
        <li><label></label><input type="text" name="code" /></li>
        <?php } ?>
        <li><label></label><input type="submit" class="button" value="Send Email" /></li>
    </ul>
    <input type="hidden" name="uid" value="<?php echo $_POST['uid'] ?>" />
</form>
<?php
include_once('includes/footer.php');
?>
