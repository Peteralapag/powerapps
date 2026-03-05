<?php
include '../init.php'; 
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$file_PATH = $_POST['filename'];
$file_name = $_POST['file_name'];
$module = $_POST['module'];
$subfolder = $_POST['subfolder'];
$username = $_SESSION['md_username'];
$md_userlevel = $_SESSION['md_userlevel'];
$md_application = $_SESSION['md_application'];
$date = date("Y-m-d H:i:s");
if($md_userlevel < 50)
{	
	if($functions->CheckAccess($username,$md_application,$module,'p_read',$db) == 0)
	{
		include '../includes/warning.php';
		exit();
	}
	else if($md_userlevel < 50)
	{	
		if($functions->AccessGranted($username,$md_application,$module,$file_name,$db) == 1)
		{ 		
			$toolbar = 1;
			$takip = 0;
			echo $functions->ExecuteAccess($md_application,$module,$file_name,$date,$db);
		} else {			
			if($functions->CheckAccess($username,$md_application,$module,'p_print',$db) == 1)
			{
				$toolbar = 1;
				$takip = 0;
			} else {
				$toolbar = 0;
				$takip = 1;
			}			
		}
	}
} else if($md_userlevel >= 50) {
	$toolbar = 1;
	$takip = 0;
	echo $functions->ExecuteAccess($md_application,$module,$file_name,$date,$db);
}
if($toolbar == 1)
{
	if($subfolder == 'Archived')
	{
		$new_access_count = ($functions->GeDownloadCount($file_name,'accessed_count',$db) + 1);
		$queryDataUpdate = "UPDATE tbl_document_properties SET accessed_count='$new_access_count' WHERE file_name='$file_name' ";
		if ($db->query($queryDataUpdate) === TRUE) { } else { echo $db->error; }
	}
}
?>
<body oncontextmenu="return false;">
<style>
.takip {position:absolute;height:px;background:#fff;background:transparent;width:calc(100% - 60px);height:100% }
.iframe-wrap {width:100%;height:100% }
.not-exist {position:absolute;top: 52%;left: 50%;-webkit-transform: translate(-50%, -50%);font-weight:bold;}
.status-bar { margin-bottom:10px; width:100% }
.frame-wrapper { display: flex;width:90vw; height:75vh;margin-bottom:10px}
</style>
<!-- div class="status-bar"><button class="btn btn-secondary pull-right" onclick="sendToEmail()">Sent to Email</button></div -->
<div class="frame-wrapper">
<?php if($takip == 1) { ?> <div class="takip"></div> 
<?php } if(file_exists($file_PATH)) { ?>
	<embed id="embeded" class="iframe-wrap"  src="<?php echo $file_PATH; ?>#toolbar=<?php echo $toolbar; ?>" style="width:100%; height: 100%"></embed>
<?php } else { ?>
	<span class="not-exist"><h1>FILE NOT EXIST</h1></span>
<?php } ?>
</div>