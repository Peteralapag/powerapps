<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$encpass = new Password;
$functions = new PageFunctions;

$organization = strtolower($_POST['organization']);
$subfolder= strtolower($_POST['subfolder']);
$file_directory = $_SERVER["DOCUMENT_ROOT"]."/Data/Written_Documents/".$organization;

$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$user_level = $_SESSION['application_userlevel'];

if(isset($_POST['search']))
{
	$search = $_POST['search'];
	$q = "file_name LIKE '%$search%' AND";
} else {	
	$q='';
}	
?>
<div class="tableFixHead ">
	<table id="filedata" style="width:100%" class="table table-hover table-striped table-bordered">
		<thead>
			<tr>
				<th style="width:30px !important">#</th>
				<th>File Name</th>
				<th>Description</th>
				<th>Date Uploaded</th>
				<th style="white-space:nowrap">Uploaded By (Me)</th>
				<th style="text-align:center;text-align:center; width:120px !important">Actions</th>
			</tr>
		</thead>		
		<tbody>
	<?PHP
		$QUERY = "SELECT * FROM tbl_archived_memo WHERE $q username='$username' AND organization='$organization' AND subfolder='$subfolder' ORDER BY date_uploaded DESC";
		$RESULTS = mysqli_query($db, $QUERY);
		if ( $RESULTS->num_rows > 0 ) 
		{
		$i=0;		
		while($ROWS = mysqli_fetch_array($RESULTS))  
		{
			$i++;
			$file_id = $ROWS['id'];
			$file_name = $ROWS['file_name'];
			$username = $ROWS['username'];
			$file_description = $ROWS['file_description'];
			$uploaded_by = $ROWS['uploaded_by'];
			$date_uploaded = date("M d, Y @ h:i A", strtotime($ROWS['date_uploaded']));
			$file_views = $ROWS['file_views'];
			$file_downloaded = $ROWS['file_downloaded'];	
			$file = basename($file_name, '.pdf');
			
			if (strlen($file_name) >= 30) {
				$filename = substr($file_name, 0, 25). " ... " . substr($file_name, -5);
			} else {
				$filename = $file_name;
			}
			
			if(file_exists($file_directory."/".$subfolder."/".$file_name))
			{
				$file_icon = '<i class="fa-solid fa-file-pdf color-red"></i>&nbsp;&nbsp;'.$filename;
			} else {
				$file_icon = '<i class="fa-solid fa-circle-exclamation color-orange"></i>&nbsp;&nbsp;'.$filename;
			}
			
			if (strlen($file_description) >= 40) {
				$description = substr($file_description, 0, 25). " ... " . substr($file_description, -5);
			} else {
				$description = $file_description;
			}
	
			$sqlSH = "SELECT * FROM tbl_shared_memo WHERE shared_id='$file_id'";
			$shResult = mysqli_query($db, $sqlSH);    
			if ( $shResult->num_rows > 0 ) 
			{
				$fa_share = '<i class="fas fa-share-alt-square" style="font-size:16px;color:blue"></i>';
				$sharing = '';
			} else {
				$fa_share = '';
				$sharing = 'disabled';
			}
	?>
<script>
	$('#tooltip<?php echo $i; ?>').hover(function()
	{
		$('#tooltips<?php echo $i; ?>').show();
	});
	$('#tooltip<?php echo $i; ?>').hover(function(){
		$('#tooltips<?php echo $i; ?>').show();
  	}, function(){
	  	$('#tooltips<?php echo $i; ?>').hide()
	});
</script>		
		<tr>
			<td style="text-align:center;font-weight:600"><?php echo $i; ?></td>
			<td style="position:relative" id="tooltip<?php echo $i; ?>">
				<?php echo $file_icon.'&nbsp;&nbsp;'.$fa_share; ?>
				<div class="tooltips" id="tooltips<?php echo $i; ?>"><?php echo $file_name; ?></div>
			</td>
			<td><?php echo $description; ?></td>
			<td><?php echo $date_uploaded; ?></td>
			<td><?php echo $uploaded_by; ?></td>
			<td style="white-space:nowrap;text-align:center">
				<div class="btn-group" role="group" aria-label="Basic example" style="margin:0 auto">
					<button class="btn btn-warning btn-sm" onclick="openInfo('<?php echo $file_id; ?>')"><i class="fa-solid fa-files color-white"></i></button>
					<button class="btn btn-primary btn-sm" onclick="openSharing('<?php echo $file_id; ?>')"><i class="fa fa-share-alt-square"></i></button>
					<button class="btn btn-danger btn-sm" onclick="deleteFile('<?php echo $file_id; ?>')"><i class="fa fa-trash"></i></button>
				</div>
			</td>
		</tr>
<?php } } else { ?>
		<tr>
			<td colspan="7" style="text-align:center; color:orange"><i class="fa fa-bell"></i> No Records</td>
		</tr>
<?php } ?>
	</table>
<div class="myfilesresults"></div>
</div>
<script>
function openInfo(file_id)
{
	$('#modaltitle').html("File Information");
	$('#modalicon').html('<i class="fa-solid fa-circle-info color-dodger"></i>');
	$.post("../Modules/Written_documents/apps/file_information.php", { file_id: file_id },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();
	});
}
function openSharing(file_id)
{	
	$('#modaltitle').html("File Sharing");
	$('#modalicon').html('<i class="fa-solid fa-share-nodes color-dodger"></i>');
	$.post("../Modules/Written_documents/apps/file_sharing.php", { file_id: file_id },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();
	}); 
}
function deleteFile(rowid)
{
	app_confirm("Delete File","Are you sure to delete this file?","warning","deleteFileYes",rowid,"red");
	return false;
}

function deleteFileYes(rowid)
{
	var mode = 'deletemyfiles';
	rms_reloaderOn("Deleting...");
	setTimeout(function()
	{
		$.post("../Modules/Written_documents/actions/actions.php", { mode: mode, rowid: rowid },
		function(data) {
			$('.myfilesresults').html(data);
			rms_reloaderOff();
		});
	},1000);
}
$(function()
{
	$('.file-data').height($('#org_content').height());
	$('#wdheader').resize(function()
	{
		$('.file-data').height($('#org_content').height());
	});
});
</script>

