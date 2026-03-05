<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$username = $_SESSION['wd_username'];
$company = $_SESSION['wd_company'];
$user_level = $_SESSION['wd_userlevel'];

	if(!isset($_POST['mode']))
	{		
		echo "Something is wrong";
		exit();
	} else {
		$mode = $_POST['mode'];
	}
	if($mode == 'sharetoorganization')
	{
		$organization = $_POST['organization'];
		$fileid = $_POST['fileid'];

		$queryShare = "SELECT * FROM tbl_archived_memo WHERE id='$fileid'";
		$shareResult = mysqli_query($db, $queryShare);    
		if ( $shareResult->num_rows > 0 ) 
		{
			while($ROWS = mysqli_fetch_array($shareResult))  
			{
				$file_name = $ROWS['file_name'];
			}
		} else {}
		
		$queryCheckShare = "SELECT * FROM tbl_shared_temp WHERE organization='$organization' AND userid='$username'";
		$checkResult = mysqli_query($db, $queryCheckShare);    
		if ( $checkResult->num_rows > 0 ) 
		{
			exit();
		}
		
		$queryCheckShare1 = "SELECT * FROM tbl_shared_memo WHERE shared_to='$organization' AND shared_by='$username' AND file_name='$file_name'";
		$checkResult1 = mysqli_query($db, $queryCheckShare1);    
		if ( $checkResult1->num_rows > 0 ) 
		{
			echo ' Already shared to '.$organization.' Department';
			print_r('
				<script>
					$(".ress").fadeIn();
				</script>
			');
			exit();
		} else {
			$queryAS = "INSERT INTO tbl_shared_temp (`file_id`,`organization`,`userid`)";
			$queryAS .= "VALUES('$fileid','$organization','$username')";
			if ($db->query($queryAS) === TRUE){
				
				print_r('
					<script>
						$(".ress").fadeOut();
					</script>
				');
	
			}
			else {echo $db->error;}
		}
	}
