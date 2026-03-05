<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$uname = $_SESSION['application_username'];
$current_password = $_POST['currpass'];
$curr_pass = $encpass->encryptedPassword($current_password,$db);
$new_password = $_POST['newpass'];
$username = mysqli_real_escape_string($db, $uname);
$password = $encpass->encryptedPassword($new_password,$db);

$sqlChange = "SELECT * FROM tbl_system_user WHERE username='$username' AND password='$curr_pass'";
$result = mysqli_query($db, $sqlChange);    
if ( $result->num_rows > 0 ) 
{ 
	$queryDataUpdate = "UPDATE tbl_system_user SET password='$password' WHERE username='$username'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		print_r('
			<script>
				app_alert("Success","You have successfuly changed your password","success","Ok","","changeyes");
			</script>
		');
	} else {
		echo $db->error;
	}
} 
else
{
	print_r('
		<script>
			app_alert("Current Password","Invalid Current Password","warning","Ok","currpass","focus");
		</script>
	');
}
mysqli_close($db);	  
