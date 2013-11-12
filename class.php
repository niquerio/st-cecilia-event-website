<?php
/*
 * KWDS Class Submission Page
 */
require_once('includes/header.php');

// Get the class cut-off date
$cutoff = $db->get_class_cutoff($kwds['KWID']);

// Make sure the user is logged in or he can't submit a class
if (!$session->isLoggedIn()) {
    echo '<div class="box error">You need to be logged in order to submit a class.</div>';
    redirect('index',$kwds['KWID']);
    include_once('includes/footer.php');
    die;
}
// Can only submit a class if it is not past the due date.
if ($db->is_class_cutoff($kwds['KWID']) AND !is_super_user()) {
    echo '<div class="box warning">This KWDS is no longer accepting class submissions.</div>';
    redirect('index',$kwds['KWID']);
    include_once('includes/footer.php');
    die;
}

// Verify that the class has a title
if (isset($_POST['name']) AND trim($_POST['name'])=="") {
    echo '<div class="box warning">Please enter a name or title for your class.</div>';
    show_class_form();
}
// Verify that the class includes a description
elseif (isset($_POST['desc']) AND trim($_POST['desc'])=="") {
    echo '<div class="box warning">Please enter a brief description about your class.</div>';
    show_class_form();
}
// Verify that the hours are entered correctly
elseif (isset($_POST['hours']) AND ($_POST['hours']*60+$_POST['minutes'] < 10)) {
    echo '<div class="box error">Your class should not be less than 10 minutes long.</div>';
    show_class_form();
}
// If a class was submitted and passes the above error checks, insert it into the data base
elseif (isset($_POST['class'])) {
    $desc=$_POST['desc'];
    $diff=$_POST['difficulty'];
    $fee=(isset($_POST['fee']))?$_POST['fee']:0;
    $hours=($_POST['hours']*60)+$_POST['minutes'];
    $kwds=$kwds['KWID'];
    $limit=(isset($_POST['limit']))?$_POST['limit']:0;
    $name=$_POST['name'];
    $notes=$_POST['notes'];
    $style=$_POST['style'];
    $type=$_POST['type'];
    $url=$_POST['url'];
    $user=$_SESSION['user_id'];
    if (isset($_POST['teacher']) AND $_POST['teacher']==1) {
        $teacher = 0;
    } else {
        $teacher = 1;
    }
    //$db->insert_class($aero, $desc, $diff, $era, $fee, $hours, $kwds, $limit, $name, $notes, $style, $teacher, $type, $url, $user);
    $db->insert_class($desc, $diff, $fee, $hours, $kwds, $limit, $name, $notes, $style, $teacher, $type, $url, $user);
    echo '<div class="box success">You have successfully submitted your class. '.$kwds.'</div>';
    redirect('index',$kwds['KWID']);
}
elseif (isset($_GET['id'])){
    $result=$db->get_class((int)($_GET['id']));
    if ($_SESSION['user_id']!=$result['UserID']) {
        $result='';
    }
    else {
       // $aero= $result['AerobicName'];
        $desc=$result['description'];
        $diff= $result['DifficultyName'];
        //$era= $result['EraName'];
        $fee= ($result['fee']!='')? $result['fee']:0;
        $hours= $result['hours'];
        $kwds= $result['KWID'];
        $limit=($result['limit']!='')? $result['limit']:0;
        $name=$result['ClassName'];
        $notes=$result['other'];
        $style= $result['StyleName'];
        $type= $result['TypeName'];
        $url=$result['url'];
        $user=$_SESSION['user_id'];
        show_class_form($cutoff);/*$name*/
    }
}
else {
    show_class_form($cutoff);
}
include_once('includes/footer.php');

// This function shows the class form
function show_class_form($cutoff) { ?>
<form class="form" name="class" action="class.php" method="post">
    <h1>New Class Submission</h1>
    <h2>Deadline for Class Submissions: <?php echo $cutoff;?></h2>
    <div class="attention box">After a class is submitted, it will not show up on the schedule page until it is approved
    by the appropriate KWDS staff. You can edit your class anytime before it is added to the schedule.</div>
    <div class="warning box">For further information about a box, hover over or click the small icons on the right side.</div>
    <ul>
        <?php if (is_class_scheduler($_SESSION['userID'],$KWID)) { ?>
        <li><label></label><input class="radio" type="checkbox" name="teacher" value="1" />I will not be teaching this class.
        <?php } ?>
        <li><label for="name">Class Name:</label><input type="text" name="name"
            <?php if (isset($_POST['name'])) { echo 'value="'.$_POST['name'].'"'; } ?>
            <?php if (isset($name)) { echo 'value="'.$name.'"'; } ?> />
            <img src="images/icons/asterix.png" alt="Required" title="Required"
                 onclick="alert('This is a required field.')" /></li>
        <li><label for="desc">Class Description:</label><textarea name="desc" cols="50" rows="10"></textarea>
            <img src="images/icons/asterix.png" alt="Required" title="Required"
                 onclick="alert('This is a required field.')" style="vertical-align:top;"/></li>
        <li><label for="hours">Length of Class:</label><?php dropdown_num('hours', 0, 8); echo 'Hrs '; dropdown_num('minutes', 0, 55, 5); echo 'Minutes'; ?>
            <img src="images/icons/exclamation.png" alt="Make a rough estimate" style="margin-left:8px"
                 title="Make a rough estimate, this may be changed later by the class coordinator to fit the schedule."
                 onclick="alert('Make a rough estimate, this may be changed later by the class coordinator to fit the schedule.')" /></li>
        <li><label for="fee">Class Fee:</label><input type="text" name="fee" />
            <img src="images/icons/question.png" alt="Optional" title="Optional"
                onclick="alert('This is optional, most classes do not have any fees.')" /></li>
        <li><label for="difficulty">Suggested Skill Level:</label><?php $db=new db; $result=$db->get_list('difficulty'); dropdown($result, 'difficulty') ?></li>
        <!--<li><label for="aerobic">Aerobic Level:</label><?php //$result=$db->get_list('aerobic'); dropdown($result, 'aerobic') ?></li> -->
        <!--<li><label for="era">Time Period:</label><?php// $result=$db->get_list('era'); dropdown($result, 'era') ?></li>-->
        <li><label for="type">Type of Class:</label><?php $result=$db->get_list('type'); dropdown($result, 'type') ?>
            <img src="images/icons/exclamation.png" alt="Important Category" title="Important Category"
                onclick="alert('This category determines to which class coordinator this class belongs.')" /></li>
        <li><label for="style">Class Format:</label><?php $result=$db->get_list('style'); dropdown($result, 'style') ?></li>
        <li><label for="limit">Attendance Limit:</label><input type="text" name="limit" />
            <img src="images/icons/question.png" alt="Optional" title="Optional. Leave this blank for no limit."
                onclick="alert('This is optional, most classes do not have any attendance limits. Leave this blank for no attendance limit.')" /></li>
        <li><label for="url">URL for Class Notes:</label><input type="text" name="url" />
                <img src="images/icons/question.png" alt="Optional" title="Optional"
                onclick="alert('This is optional. If you have a webpage for your class, enter the URL here.')" /></li>
        <li><label for="notes">Special Notes:<br />(This section will only be seen by the class coordinators)</label>
            <textarea name="notes" cols="50" rows="10"><?php echo $notes ?></textarea>
            <img src="images/icons/information.png" style="vertical-align:top;" alt="Special Notes" 
                 title="Enter information such as schedule preferences/avoidances, arrival/departure dates, special needs, etc."
                 onclick="alert('Enter information such as schedule preferences/avoidances, arrival/departure dates, special needs, etc.')" /></li>
        <!--<li><label for="submit">KWDS Submission:</label> <?php /*$result=$db->get_kwds_submissions(); dropdown($result, 'kwds') */ ?></li>-->
        <li><label> </label><input type="submit" class="button" name="class" value="Submit Class" /></li>
    </ul>
</form>
<?php } ?>
