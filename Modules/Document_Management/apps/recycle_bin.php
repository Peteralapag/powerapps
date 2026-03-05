<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['md_username'];
$md_userlevel = $_SESSION['md_userlevel'];
if($md_userlevel >= 80)
{
	$q = "";
} else if($md_userlevel <= 50)
{
	$q = "WHERE username='$username'";
}
?>
<style>
.recycle-wrapper { overflow: auto; max-height: calc(100vh - 300px); width:1000px; font-family: sans-serif}
.recycle-wrapper thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff }
.recycle-wrapper table  { border-collapse: collapse;}
.recycle-wrapper th, .recycle-wrapper td { font-size:12px; white-space:nowrap }
.recycle-wrapper td input {
	font-size:12px;
}
</style>
<div class="recycle-wrapper">
	<table style="width: 100%" class="table">
		<thead>
		<tr>
			<th style="width:50px;text-align:center">#</th>
			<th>File Name</th>
			<th>Original Location</th>
			<th>Date Deleted</th>
			<th>Actions</th>
		</tr>
		</thead>
		<tbody>
<?php
	$checkPolicy = "SELECT * FROM tbl_document_bin $q ORDER BY id DESC";
	$pRes = mysqli_query($db, $checkPolicy);    
	if ( $pRes->num_rows > 0 ) 
	{
		$b=0;
		while($ROWS = mysqli_fetch_array($pRes))  
		{
			$b++;
?>		
		<tr>
			<td style="text-align:center"><?php echo $b;?></td>
			<td><input type="text" class="form-control" value="<?php echo $ROWS['file_name']; ?>" readonly></td>
			<td><input type="text" class="form-control" value="<?php echo $ROWS['path']; ?>" disabled></td>
			<td><?php echo date("F d, Y @ h:i A", strtotime($ROWS['date_deleted'])); ?></td>
			<td style="width:80px;text-align:right"><button class="btn btn-secondary btn-sm" onclick="restoreFiles()">Restore</button></td>
		</tr>
<?php } } else { ?>		
		<tr>
			<td colspan="5" style="text-align:center">No Deleted files found</td>
		</tr>
<?php } ?>
	</tbody>
	</table>
</div>
<script>
function restoreFiles()
{
	swal("Access Denied", "This function is under development", "warning");
}
</script>