<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
} else {
	print_r('
		<script>
			app_alert("Warning"," The Mode you are trying to pass does not exist","warning","Ok","","no");
		</script>
	');
	exit();
}
$time_stamp = date("Y-m-d H:i:s");
/* ############################################### UNSHARE FILE  ################################# */
if($mode=='unsharefile')
{	
	$rowid = $_POST['rowid'];
	$file_id = $_POST['file_id'];
	$sqldelItem = "DELETE FROM tbl_shared_memo WHERE id='$rowid'";
	if ($db->query($sqldelItem) === TRUE)
	{ 
		print_r('
			<script>
				openInfo("'.$file_id.'");
			</script>
		');
	} 
	else { echo $db-error; }
}
/* ############################################### DELETE FILE  ################################# */
if($mode=='deletemyfiles')
{
	$rowid = $_POST['rowid'];
	if($functions->checkSharing($rowid,$db) == 1) 
	{
		print_r('
			<script>
				swal("System Message","It is not possible to delete files that have been shared with other organizations.","warning");
			</script>
		');
		exit();
	} else {
		if($functions->deleteArchiveMemo($rowid,$db) == 1)
		{
			print_r('
				<script>
					load_content(sessionStorage.organization,sessionStorage.subfolder,sessionStorage.page);
					swal("System Message","The file has been deleted successfully.","warning");
				</script>
			');
		} else {
			$errdb = $functions->deleteArchiveMemo($rowid,$db);
			print_r('
				<script>
					swal("System Message","'.$errdb.'.","warning");
				</script>
			');
		}
	}
}
/* ############################################### SAVE  ################################# */
if($mode=='saveorganization')
{
	$rowid = $_POST['rowid'];
	$value = $_POST['value'];
	$table = $_POST['table'];
	$column = $_POST['column'];
	if($_POST['cluster'] != '')
	{
		$cluster = $_POST['cluster'];
		$q = "$column='$value', location='$cluster'";
	} else {
		$q = "$column='$value'";
	}
	$queryDataUpdate = "UPDATE $table SET $q WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		$params = str_replace("tbl_", "", $table);
		print_r('
			<script>
				var params = "'.$params.'";
				showFolder(params);
			</script>
		');
	} else {
		echo "ERROR::: ".$db->error;
	}

}
