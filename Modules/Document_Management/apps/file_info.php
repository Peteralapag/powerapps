<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$path  = str_replace("../../../../", "", $_POST['path']); 
$username = $_SESSION['md_username'];
$account_name = $_SESSION['md_appnameuser'];
$md_userlevel = $_SESSION['md_userlevel'];
$filename = $_POST['filename'];
$module = $_POST['module'];
$subfolder = $_POST['subfolder'];
$md_application = $_SESSION['md_application'];
$file_name = $_SERVER['DOCUMENT_ROOT']."/".$path."/".$filename;
$file_size = $functions->GetFileSize($file_name);
$file_date = $functions->GetFileDate($file_name);
$fname = str_replace("../../../../", "../", $_POST['path']).$filename;
$author = $functions->GetFileProperties("author",$filename,$db);
$request_pending = 0;
//$request_btn = 0;
//$request_cancel = 0;
$req_type = 0;
if($md_userlevel <= 50)
{	
	if($functions->CheckAccess($username,$md_application,$module,'p_view',$db) == 0)
	{
		include '../../../includes/warning.php';
		exit();
	}
}
	$checkPolicy = "SELECT * FROM tbl_app_request WHERE applications='$md_application' AND modules='$module' AND file_name='$filename' AND requested_by='$username' ORDER BY id DESC LIMIT 1";
	$pRes = mysqli_query($db, $checkPolicy);    
	if ( $pRes->num_rows > 0 ) 
	{
		while($ROWS = mysqli_fetch_array($pRes))  
		{
			$rowid = $ROWS['id'];
			$req_type = $ROWS['request_type'];
			$approved = $ROWS['approved'];
			$executed = $ROWS['executed'];
			if($subfolder == 'Archived')			
			{
				if($ROWS['approved'] == 0 AND $ROWS['executed'] == 0)	
				{
					$request_btn = 0;
					$request_cancel = 1;
					$request_pending = 1;
					// echo "A";
				}
				if($ROWS['approved'] == 1 AND $ROWS['executed'] == 0)	
				{
					$request_btn = 0;
					$request_cancel = 1;
					$request_download = 0;
					$request_pending = 0;
					//echo "B";
				}
				if($ROWS['approved'] == 1 AND $ROWS['executed'] == 1)	
				{
					$request_btn = 1;
					$request_cancel = 0;
					$request_download = 0;
					$request_pending = 0;
					// echo "c";
				}
			}
			if($subfolder == 'Raw')			
			{
				if($ROWS['approved'] == 0 AND $ROWS['executed'] == 0)	
				{
					$request_btn = 0;
					$request_cancel = 1;
					$request_pending = 1;
//					echo "A";
				}
				if($ROWS['approved'] == 1 AND $ROWS['executed'] == 0)	
				{
					$request_btn = 0;
					$request_cancel = 1;
					$request_download = 0;
					$request_pending = 0;
//					echo "B";
				}
				if($ROWS['approved'] == 1 AND $ROWS['executed'] == 1)	
				{
					$request_btn = 1;
					$request_cancel = 0;
					$request_download = 0;
					$request_pending = 0;
//					echo "c";
				}
				if($ROWS['approved'] == 2 AND $ROWS['executed'] == 1)	
				{
					$request_btn = 1;
					$request_cancel = 0;
					$request_download = 0;
					$request_pending = 0;
//					echo "c";
				}
			}
		}
	} else {
		$request_text = '<i class="fa-solid fa-envelope"></i>&nbsp;&nbsp;Request Access';
		$request_btn = 1;
		$request_function = 'onclick=createRequest(\''.$filename.'\')';
		$request_cancel = 0;
		$request_download = 0;
		// echo "E";
	}
?>
<style>
.file-info-wrapper {
	width:550px;
	margin-bottom:10px;
}
hr {
	margin:8px;
}
</style>
<div class="file-info-wrapper">	
	<table style="width: 100%">
		<tr>
			<td style="width:130px">Department</td>
			<td><?php echo $module; ?></td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>

		<tr>
			<td style="width:130px">File Name</td>
			<td><input type="text" class="form-control form-control-sm" value="<?php echo str_replace("_"," ",$filename); ?>" readonly></td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>Size</td>
			<td style="font-size:14px"><?php echo $file_size; ?></td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>Uploaded</td>
			<td style="font-size:14px"><?php echo $file_date; ?></td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>Author</td>
			<td style="font-size:14px"><?php echo $functions->GetFileProperties('author',$filename,$db); ?></td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
	<?php if($subfolder == 'Archived') { ?>
		<tr>
			<td>Accessed:</td>
			<td style="font-size:14px">This file has been Accessed <?php echo $functions->GetFileProperties('accessed_count',$filename,$db); ?> Times</td>
		</tr>
	<?php } else if($subfolder == 'Raw') { ?>
		<tr>
			<td>Downloaded</td>
			<td style="font-size:14px">This file has been Downloaded <?php echo $functions->GetFileProperties('downloaded_count',$filename,$db); ?> Times</td>
		</tr>
	<?php } ?>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
<?php
	if($functions->AccessGranted($username,$md_application,$module,$filename,$db) == 1)
	{
?>
		<tr>
			<td colspan="2">
				<div style="background:#b8c4d1; padding:5px;color:red;border:1px solid #8594a6;border-radius:5px;text-align:center">
					<em>Request Granted</em>
				</div>
			</td>
		</tr>
<?php } if($request_pending == 1) { ?>
		<tr>
			<td colspan="2">
				<div style="background:#b8c4d1; padding:5px;color:#fff;border:1px solid #8594a6;border-radius:5px;text-align:center">
					<em>Request Pending</em>
				</div>
			</td>
		</tr>
<?php }?>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:right">
				<?php include $_SERVER['DOCUMENT_ROOT'].'/Modules/Document_Management/includes/file_info_buttons.php'; ?>
			</td>
		</tr>
	</table>
<div class="results"></div>
</div>
<script>
function renameThisFile(fullpath,filename)
{
	var module = '<?php echo $module; ?>';
	$('#modaliconPDF').html('<i class="fa-solid fa-file-pen color-dodger"></i>');
	$('#modaltitlePDF').html('<?php echo str_replace("_", " ", $filename); ?>');	
	$.post('../../../Modules/Document_Management/apps/rename_file_form.php', { fullpath: fullpath, filename: filename, module: module },
	function(data) {
		$('#formmodal_page').html(data);	
		$('#formmodal').show();
	});	
}
function DownloadThisFile(fullpath,filename)
{
	var module = '<?php echo $module; ?>';
	rms_reloaderOn("Preparing Download...");
	setTimeout(function()
	{
		$.post('../../../Modules/Document_Management/actions/download_process.php', { fullpath: fullpath, filename: filename, module: module },
		function(data) {
			$('.results').html(data);	
			rms_reloaderOff();
		});
	},1000);
}
function cancelRequest(rowid)
{
	app_confirm("Cancel Request","Are you sure to cancel your request?","warning","cancelRequestYes",rowid,"red");
	return false;
}
function cancelRequestYes(rowid)
{
	var mode = 'deleterequest';
	rms_reloaderOn("Canceling Request...");
	setTimeout(function()
	{
		$.post('../../../Modules/Document_Management/actions/actions.php', { mode: mode, rowid: rowid },
		function(data) {
			$('.results').html(data);	
			rms_reloaderOff();
		});
	},1000);
}
function createRequest(filename)
{
	var mode = 'createrequest';
	var application = '<?php echo $md_application; ?>';
	var module = '<?php echo $module; ?>';
	var filename = '<?php echo $filename; ?>';
	var account_name = '<?php echo $account_name; ?>';
	var requested_by = '<?php echo $username; ?>';
	$('#modalicon').html('<i class="fa-solid fa-arrow-right color-dodger"></i>');
	$('#modaltitle').html("Create Full Access Request");
	rms_reloaderOn('Loading Request Form...');
	setTimeout(function()
	{
		$.post('../../../Modules/Document_Management/apps/request_form.php', { mode: mode, filename: filename, application: application, module: module, account_name: account_name, requested_by: requested_by },
		function(data) {
			$('#formmodal_page').html(data);
			$('#formmodal').show();
			rms_reloaderOff();	
		});	
	},1000);		
}
function viewThisFile(filename,file_name)
{
	var module = '<?php echo $module; ?>';
	var subfolder = '<?php echo $subfolder; ?>';
	$('#modaliconPDF').html('<i class="fa-solid fa-file-pdf color-red"></i>');
	$('#modaltitlePDF').html('<?php echo str_replace("_", " ", $filename); ?>');	
	$.post('../../../pages/md_pdf_viewer.php', { module: module,  filename: filename, file_name: file_name, subfolder: subfolder },
	function(data) {
		$('#pdfviewer_page').html(data);	
		$('#pdfviewer').show();
	});	
}
function deleteFile(filename)
{
	app_confirm("Delete File","Are you sure to delete this file.?","warning","deleteFileYes",filename,"red");
}
function deleteFileYes(filename)
{
	var mode = 'deletethisfile';
	var module = '<?php echo $module ?>';
	var path = '<?php echo $path ?>';
	var username = '<?php echo $username; ?>';
	var application = sessionStorage.module;
	var author = '<?php echo $author; ?>';
	rms_reloaderOn('Deleting File...');
	setTimeout(function()
	{
		$.post('../../../Modules/Document_Management/actions/delete_file_process.php', { mode: mode, application: application, author: author, filename: filename, username: username, module: module, path: path },
		function(data) {
			$('.results').html(data);	
			closeModal('formmodal');
			$('#' + sessionStorage.slidercount).trigger('click');
			rms_reloaderOff();				
		});	
	},1000);	
}
</script>