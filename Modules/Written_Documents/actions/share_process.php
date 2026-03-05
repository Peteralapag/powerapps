<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$username = $_SESSION['wd_username'];
$fileowner = $_SESSION['wd_appnameuser'];

$file_id = $_POST['fileid'];
$description = $_POST['description'];
$tocompany = $_POST['company'];
$date_now = date("Y-m-d H:i:s");

$queryShareProcess = "SELECT * FROM tbl_archived_memo WHERE id='$file_id'";
$sResult = mysqli_query($db, $queryShareProcess);    
if ( $sResult->num_rows > 0 ) 
{
	$i=0;
	$data = array();
	while($ROW = mysqli_fetch_array($sResult))  
	{
		$sid = $ROW['id'];
		$company = $ROW['company'];
		$organization = $ROW['organization'];
		$subfolder = $ROW['subfolder'];
		$file_name = $ROW['file_name'];
		$file_description = $ROW['file_description'];
		$uploaded_by = $ROW['uploaded_by'];
		$username = $ROW['username'];
		$date_uploaded = $ROW['date_uploaded'];
		if($description == '')
		{
			$desc = $file_description;
		} else {
			$desc = $description;
		}
		$qShareProcess = "SELECT * FROM tbl_shared_temp WHERE file_id='$file_id' AND userid='$username'";
		$qsResult = mysqli_query($db, $qShareProcess);    
		if ( $qsResult->num_rows > 0 ) 
		{
			$i=0;
			$data = array();
			while($ROWS = mysqli_fetch_array($qsResult))  
			{
				$shared_to = strtolower($ROWS['organization']);
				echo $shared_to;
				$data[] = "('$sid','$shared_to','$fileowner','$organization','$date_now','$tocompany','$organization','$subfolder','$file_name','$desc','$fileowner','$username','$date_uploaded')";
			}
		} else {
			echo "Please select at least one department/organization to share.";
			exit();
		}
	}
	$sqls = "INSERT INTO tbl_shared_memo (`shared_id`,`shared_to`,`shared_by`,`shared_from`,`date_shared`,`company`,`organization`,`subfolder`,`file_name`,`file_description`,`uploaded_by`,`username`,`date_uploaded`)";
	$sqls .= "VALUES ".implode(', ', $data);
	if ($db->query($sqls) === TRUE)
	{
		deleteTemp($sid,$db);
		echo "Your file has been successfuly shared.";
		print_r('
			<script>
				$("#mf").trigger("click");
			</script>
		');

	} else {
		echo "Error ". $db->error;
	}	
		
} else {
	echo "File not found";
}
function deleteTemp($sid,$db)
{
	$sqldelItem = "DELETE FROM tbl_shared_temp WHERE file_id='$sid'";
	if ($db->query($sqldelItem) === TRUE){ }else{echo $db->error;}
}
