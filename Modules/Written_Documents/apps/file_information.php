<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$functions = new PageFunctions;
$file_id = $_POST['file_id'];
?>
<?php
	$INFOQUERY = "SELECT * FROM tbl_archived_memo WHERE id='$file_id'";
	$INFORESULTS = mysqli_query($db, $INFOQUERY);    
	while($INFO = mysqli_fetch_array($INFORESULTS))  
	{
		$file_name = $INFO['file_name'];
		$file_description = $INFO['file_description'];
		$org = $INFO['organization'];
	}
?>
<div class="file-information">
	<table style="width: 100%" class="table-bordered">
		<tr>
			<th>File Name</th>
			<td><input type="text" class="form-control" value="<?php echo $file_name; ?>" readonly></td>
		</tr>
		<tr>
			<td colspan="2" style="border:0;height:5px"></td>
		</tr>
		<tr>
			<th>File Description</th>
			<td><input type="text" class="form-control" value="<?php echo $file_description; ?>" readonly></td>
			
		</tr>
	</table>
	<div class="sharing-nav">
		<button class="btn btn-success btn-sm" onclick="openThisFile('<?php echo $file_id; ?>','<?php echo $file_name; ?>','<?php echo $org; ?>')">Open this File</button>
		<button class="btn btn-danger btn-sm">Email this File</button>
	</div>
	<hr>
	<div class="fieldset">
		<div class="legend"><i class="fa-solid fa-share-nodes color-dodger"></i>&nbsp;&nbsp;Shared Files</div>		
		<div class="share-table">
			<table style="width: 100%">
				<tr>
					<th style="width: 30px;text-align:center">#</th>
					<th>Shared To Org.</th>
					<th>Shared Date</th>
					<th style="width:80px">Action</th>
				</tr>
<?php
	$SHAREDQUERY = "SELECT * FROM tbl_shared_memo WHERE shared_id='$file_id'";
	$SHAREDRESULTS = mysqli_query($db, $SHAREDQUERY);    
	if ( $SHAREDRESULTS->num_rows > 0 ) 
	{
		$n=0;
		while($SHARE = mysqli_fetch_array($SHAREDRESULTS))  
		{
			$n++;
			$rowid = $SHARE['id'];
			$shared_to = str_replace("-"," ",$SHARE['shared_to']);
			$shared_date = date("M. d, Y @ h:m", strtotime($SHARE['date_shared']));
		
?>
			<tr>
				<td style="text-align:center;font-weight:600"><?php echo $n; ?></td>
				<td><?php echo ucwords($shared_to); ?></td>
				<td><?php echo $shared_date; ?></td>
				<td style="text-align:center"><span class="btnunshare" onclick="unshareFile('<?php echo $rowid; ?>','<?php echo $file_id; ?>')">Un-Share</span></td>
			</tr>
<?php } } else { ?>
			<tr>
				<td colspan="4" style="text-align:center; color:#232323;padding:10px"><i class="fa fa-bell color-orange"></i> This file has not been shared with any organization.</td>
			</tr>
<?php } ?>
		</table>
	</div>			
	</div>
	<div class="results"></div>
</div>
<script>
function unshareFile(rowid,file_id)
{
	app_confirm('Warning','Are you sure you want to unsahare this file?','warning','deleteShareFile',rowid,file_id);
}
function unshareFileYes(rowid,file_id)
{
	var mode = 'unsharefile';
	$.post("../Modules/Written_documents/actions/actions.php", { mode: mode, rowid: rowid, file_id: file_id },
	function(data) {
		$('.results').html(data);
		$("#mf").trigger("click");	
	}); 
}
</script>
