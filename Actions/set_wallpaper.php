<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$company = $_SESSION['application_company'];
$username = $_SESSION['application_username'];
$column = $_POST['column'];
$wallpaper = $_POST['wp'];

if($column == 'desktop')
{
	$update = "company='$company',username='$username',desktop='$wallpaper'";
	$col = "`company`,`username`,`desktop`";
	$insert = "'$company','$username','$wallpaper'";
}
else if($column == 'login')
{
	$update = "company='$company',username='$username',login='$wallpaper'";
	$col = "`company`,`username`,`login`";
	$insert = "'$company','$username','$wallpaper'";
}
$checkQuery = "SELECT * FROM tbl_user_wallpaper WHERE username='$username'";
$pRes = mysqli_query($db, $checkQuery);    
if ( $pRes->num_rows > 0 ) 
{
	$queryDataUpdate = "UPDATE tbl_user_wallpaper SET $update WHERE username='$username'";
	if ($db->query($queryDataUpdate) === TRUE) { } else { echo $db->error; }	
} 
else
{
	$queryInsert = "INSERT INTO tbl_user_wallpaper ($col)";
	$queryInsert .= "VALUES($insert)";
	if ($db->query($queryInsert) === TRUE) { } else { echo $db->error; }
}
mysqli_close($db);