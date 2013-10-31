<?php
/*
 * KWDS Proceedings
 */
require_once('includes/header.php');
$info = $db->get_kwds_field('proceedings',$kwds['KWID']);

// Files can only be uploaded for classes that are accepted
if ($kwds['KWID'] >= $db->get_next_kwds()) {
    $classes = $db->get_accepted_classes($_SESSION['user_id'], $kwds['KWID']);
}
// If files are uploaded, check the size and type
$allowedExts = array("pdf", "doc", "docx", "rtf","docm","txt","htm","html","mht","pub","wpd","wps");
if (count($classes) > 0 AND count($_FILES) > 0) {
    foreach ($classes as $class) {
        $extension = end(explode(".", $_FILES["c".$class['id']]["name"]));
        if ($_FILES["c".$class['id']]["size"] < 500000 && in_array($extension, $allowedExts)) {
            if ($_FILES["c".$class['id']]["error"] > 0) {
                echo "Return Code: " . $_FILES["c".$class['id']]["error"] . "<br>";
            }
            else {
                // If no errors found, then upload the file
                $_FILES["c".$class['id']]["name"] = "c".$class['id'].'.'.$extension;
                move_uploaded_file($_FILES["c".$class['id']]["tmp_name"],
                    "class_uploads/" . $_FILES["c".$class['id']]["name"]);
                $db->update_proceeding_upload($class['id'],$extension);
                $class['upload']=$extension;
                echo "<div class=\"success box\">File uploaded successfully for ".$class['name'].".</div>";

            }
        }
        elseif ($_FILES["c".$class['id']]["name"]=='') {
        }
        else {
            echo '<div class="error box">Invalid file type for the class '.$class['name'].'.</div>';
        }
    }
}
// If the Proceedings Editor pressed the update button, make the updates.
$extension = $db->get_proceeding_extension($kwds['KWID']);
if ($_POST['update'] == "Update") {
    $info = $_POST['info'];
    $extension = end(explode(".", $_FILES["k".$kwds['KWID']]["name"]));
    $_FILES["k".$kwds['KWID']]["name"] = "k".$kwds['KWID'].'.'.$extension;
    move_uploaded_file($_FILES["k".$kwds['KWID']]["tmp_name"],
        "class_uploads/" . $_FILES["k".$kwds['KWID']]["name"]);
    $db->update_proceedings($info, $kwds['KWID'],$extension);
    echo "<div class=\"success box\">The proceedings has been successfully uploaded.</div>";


}
// If the Proceedings Editor pressed the edit button, display this:
if ($_POST['edit'] == "Edit") {
    $info = $db->get_kwds_field('proceedings', $kwds['KWID']);
    echo '<h1>Proceedings</h1>';
    echo '<form class="form" action="proceedings.php" method="post" enctype="multipart/form-data">
        <ul><li><label>Information:</label><textarea name="info">'.$info.'</textarea></li>
            <li><label>Upload Proceedings:</label><input type="file" name = "k'.$kwds['KWID'].'" id="k'.$kwds['KWID'].'" /></li>
            <li><label></label><input class=button name="update" type="submit" value="Update" /></li>
        </ul></form>';
    include_once('includes/footer.php');

}
else {
// If the proceeding editor is logged in, then show the edit button.
if ($kwds['KWID']>=$db->get_next_kwds() AND is_proceeding_editor($_SESSION['user_id'], $kwds['KWID'])) {
    echo '<form action="proceedings.php" method="post">
        <div class="right"><input class="button" name="edit" type="submit" value="Edit" /></div></form>';
}
// Start displaying the actual proceedings page.
echo '<h1>Proceedings</h1>';
if (isset($extension) AND $extension != '') {
    echo '<h2><a href="class_uploads/k'.$kwds['KWID'].'.'.$extension.'">Download the Proceedings</a></h2>';
}

if ($info=='') echo '<p>There is no further information to report.</p>';
else echo '<div>'.$info.'</div>';

if ($kwds['KWID']>=$db->get_next_kwds() AND count($classes) > 0) {
    $classes = $db->get_accepted_classes($_SESSION['user_id'], $kwds['KWID']);

    echo '<br /><h2>Proceeding Submissions</h2>
        <p>For any class instructor intending on submitting class notes for the proceedings, the
        <a href="http://sca.org/docs/pdf/ReleaseCreative.pdf">SCA CREATIVE WORK COPYRIGHT ASSIGNMENT/GRANT OF USE FORM</a>
        will need to be completed. Contact your class coordinator or the proceedings editor
        to determine where the forms should be submitted.</p>
        <p>Below, you can upload your class notes to the website for the proceedings editor to obtain them.</p>
        <form class="form" action="proceedings.php" method="post" enctype="multipart/form-data">
        <ul>';

    foreach ($classes as $class) {
        echo '<li><input type="file" name = "c'.$class['id'].'" id="c'.$class['id'].'" /> ';
        if ($class['upload'] != NULL) {
            echo '<img src="images/icons/accept.png" alt="File already uploaded." title="File already uploaded."
                onclick = "alert(\'Your file has already been uploaded.\')" /> ';
        }
        else {
            echo '<img src="images/icons/exclamation.png" alt="You may upload a file for the proceedings."
                title="You may upload a file for the proceedings."
                onclick = "alert(\'You may upload a file for the proceedings.\')" /> ';
        }
        echo $class['name'].'</li>';
    }
    echo '<input class="button" type="submit" name="submit" value="Submit">
</ul></form>';
}
// If you are the proceedings editor, you may download the class files that were uploaded.
if ($kwds['KWID']>=$db->get_next_kwds() AND is_proceeding_editor($_SESSION['user_id'], $kwds['KWID'])) {
    $classes = $db->get_class_info($kwds['KWID']);
    echo '<br /><hr /><h2>Class Listing</h2>';
    echo '<ul style="list-style:none">';
    foreach ($classes as $class) {
        echo '<li>';
        if ($class['ClassUpload']!= NULL) {
            echo '<img src="images/icons/accept.png" alt="File uploaded" title="File uploaded" />
                <a href="class_uploads/c'.$class['ClassID'].'.'.$class['ClassUpload'].'">';
        }
        else {
            echo '<img src="images/icons/error.png" alt="File NOT uploaded" title="File NOT uploaded" />';
        }

        echo ' <span class="bold">'.$class['ClassName'].'</span>';
        if ($class['ClassUpload']!= NULL) {
            echo '</a>';
        }
        echo ' - '.$class['SCAFirst'].' '.$class['SCALast'].' ('.$class['MundaneFirst'].' '.$class['MundaneLast'].')';
        echo '</li>';
    }
}

include_once('includes/footer.php');
}
?>
