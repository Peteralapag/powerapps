<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['md_username'];
$md_application = $_SESSION['md_application'];
$access = $_POST['access'];
if($functions->CheckAccess($username,$md_application,$access,$db) == 1);
{
	
}
mysqli_close($db);
?>