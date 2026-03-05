<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;

$organization = $_POST['organization'];
$subfolder= $_POST['subfolder'];
$file_directory = $_SERVER["DOCUMENT_ROOT"]."/Data/Written_Documents/".$organization;

$upload_directory = $file_directory."/".$subfolder;

$file = basename($_FILES["file"]["name"]); 
$file_description = $_POST['description'];
$company = $_SESSION['wd_company'];
$uploaded_by = $_SESSION['wd_appnameuser'];
$username = $_SESSION['wd_username'];
$date_uploaded = date("Y-m-d H:i:s");
$file_date = date("His");
$file = basename($file, '.pdf');
$_file_name = $file."_".$file_date.".pdf";

$targetFilePath = $upload_directory."/". $file."_".$file_date.".pdf"; 


$query = "INSERT INTO tbl_archived_memo (company,file_name,file_description,organization,subfolder,uploaded_by,username,date_uploaded)";
$query .= "VALUES('$company','$_file_name','$file_description','$organization','$subfolder','$uploaded_by','$username','$date_uploaded')";
if ($db->query($query) === TRUE)
{
	print_r('
		<script>
			var file_name = "'.$file.'";
			swal("System Message", file_name + " Has been successfuly uploaded","success");
			load_content(sessionStorage.organization,sessionStorage.subfolder,sessionStorage.page);
		//	closeModal("formmodal");
		</script>		
	');
	if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {}
} else {
	$err = $db->error;	
	print_r('
		<script>
			swal("System Message","'.$err.'","warning");
		</script>
	');
}




?>