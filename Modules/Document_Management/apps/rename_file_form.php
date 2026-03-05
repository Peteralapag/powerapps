<?php
$fullpath = $_POST['fullpath'];
$module = $_POST['module'];
$filename = $_POST['filename'];
?>
<table style="width: 100%; width:500px">
	<tr>
		<th style="width: 99px">File Name</th>
		<td style="width:8px;"></td>
		<td><input id="oldfilename" type="text" class="form-control" value="<?php echo $filename; ?>" readonly></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<th style="width: 99px">New Name</th>
		<td style="width:8px;"></td>
		<td><input id="filename" type="text" class="form-control" placeholder="Enter new file name" autocomplete="off"></td>
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align:right">
			<button class="btn btn-primary" onclick="renameFileNow()">Rename</button>
			<button class="btn btn-danger">Cancel</button>
		</td>
	</tr>
</table>
<div class="results"></div>
<script>
function renameFileNow()
{
	var fullpath = '<?php echo $fullpath; ?>';
	var module = '<?php echo $module; ?>';
	var oldfilename = $('#oldfilename').val();
	var filename = $('#filename').val();
	if(filename == '')
	{
		app_alert("Invalid file name","Please enter new File Name.","warning","Ok","filename","focus");
		return false;
	}
	rms_reloaderOn("Renaming! Please Wait...");
	setTimeout(function()
	{
		$.post('../../../Modules/Document_Management/actions/rename_process.php', { fullpath: fullpath, filename: filename, oldfilename: oldfilename, module: module },
		function(data) {
			$('.results').html(data);	
	 		rms_reloaderOff();
	 		$('#' + sessionStorage.slidercount).trigger('click');
		});
	},1000);
}
</script>