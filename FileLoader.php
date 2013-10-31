<?php
/*
 * 
 */
/*
require_once('includes/header.php');
if ($_FILES["file"]["error"] > 0)
    {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {

if ($_FILES > 0 AND sanit(md5($_POST["password"])) == '2510d50a0da03f0d05f81bdd2d82d54b') {
    if (file_exists("upload/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      echo $_FILES["file"]["name"];
    move_uploaded_file($_FILES["file"]["tmp_name"],"uploads/" . $_FILES["file"]["name"]);
    echo " Success";
      }
}
    }
?>
<form class="form" enctype="multipart/form-data" action="FileLoader.php" method="post">
    <ul>
        <li><label>File:</label><input type="file" name="file" id="file" /></li>
        <li><label>Password:</label><input type="password" name="password" /></li>
        <li><label></label><input type="submit" name="Submit" /></li>
    </ul>
</form>
<?php
include_once('includes/footer.php');
*/
?>