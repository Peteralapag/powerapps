<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if($_SESSION['application_userlevel'] >= 80) {
	echo 1;
} else {
	$sqlLogin = "SELECT * FROM tbl_system_permission WHERE p_view=1";
	$result = mysqli_query($db, $sqlLogin);    
    if ( $result->num_rows > 0 ) 
    {
		echo 1;		
	} else {
		echo 0;
	}
}
mysqli_close($db);
?>