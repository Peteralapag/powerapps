<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
/*
	$uname = $_POST['username'];
	$pass = $_POST['password'];
	$user_app = "APPLICATION MANAGEMENT";
	$username = mysqli_real_escape_string($db, $uname);
	$password = $encpass->encryptedPassword($pass,$db);
*/	
$sqlCheckUser = "SELECT * FROM tbl_system_user WHERE username='$username'";
$UserResults = mysqli_query($db, $sqlCheckUser);    
if ( $UserResults->num_rows > 0 ) 
{ 
    while($USEROWS = mysqli_fetch_array($UserResults))  
	{
		if($USEROWS['password'] == 'bc996c04dd24855c6c7987b2c5643489729be55bf966ee755a4b600d04a42eba')
		{
			echo 1;
		} else {
			echo 0;
		}
	}			
} else {
	echo 0;
}
mysqli_close($db);