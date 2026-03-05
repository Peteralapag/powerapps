<?php 
$full_path = $_POST['department']."/".$_POST['subfolder'];
$department = $_POST['department'];
$subfolder = $_POST['subfolder'];
?>
<style>
.browser-parent {display: flex;height:100%;flex-direction: column;}
.browser-header {border:1px solid #aeaeae;background:#f1f1f1;padding:5px;margin-bottom:10px;border-radius:7px;}
.#archivedfolder2 {display: none;}
#lastfolder {display: none;}
</style>
<div class="browser-parent">
	<div class="browser-header">	
		<table style="width: 100%">
			<tr>
				<td>&nbsp;<i class="fa-solid fa-folder-tree color-orange"></i>&nbsp;&nbsp;<?php echo $department; ?> / <?php echo $subfolder; ?></td>
				<input id="department" type="hidden" value="<?php echo $department; ?>">
				<input id="subfolder" type="hidden" value="<?php echo $subfolder; ?>">
				<!--td style="width:5px;text-align:center;padding:5px">-</td>
				<td style="width:300px"></td>
				<td style="width:5px;text-align:center;padding:5px"></td>
				<td style="width:250px"></td-->
				<input id="fullpath" type="hidden" class="form-control form-control-sm" value="">				
				<td style="text-align:right">
					<button id="uploadshow" class="btn btn-warning btn-sm color-white" onclick="UploadFile('<?php echo $subfolder; ?>')"><i class="fa-solid fa-cloud-arrow-up"></i>&nbsp;&nbsp;Upload File</button>
					<button class="btn btn-success btn-sm" onclick="recycleBin()"><i class="fa-solid fa-recycle"></i></button>
				</td>
			</tr>
		</table>	
	</div>
	<div class="browser-container" id="browser"></div>
	<div id="results"></div>
</div>	
<script>
function recycleBin()
{
	$('#modalicon').html('<i class="fa-solid fa-recycle color-green"></i>');
	$('#modaltitle').html("Recycle Bin");
	$.post("../Modules/Document_Management/apps/recycle_bin.php", { },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
$(function()
{
	var path = '<?php echo $full_path; ?>';
	var department = '<?php echo $department; ?>';
	$.post('../../../../Modules/Document_Management/modules/file/index.php', { path: path, department: department },
	function(data) {
		$('#browser').html(data);		
	});	
});
function UploadFile(subfolder)
{
	var fullpath = $('#fullpath').val();
	if(fullpath == '')
	{
		swal("System Message","Please select folder","warning");
		return false;
	}
	if(subfolder == 'Archived')
	{
		var folder = fullpath.split("/").pop();	
		$('#modalicon').html('<i class="fa-solid fa-cloud-arrow-up color-dodger"></i>');
		$('#modaltitle').html("Upload PDF File");
		$.post("../Modules/Document_Management/apps/upload_pdf.php", { fullpath: fullpath, folder: folder, subfolder: subfolder },
		function(data) {
			$('#formmodal_page').html(data);
			$('#formmodal').show();
		});
	} else if(subfolder == 'Raw') {
		var folder = fullpath.split("/").pop();	
		$('#modalicon').html('<i class="fa-solid fa-cloud-arrow-up color-dodger"></i>');
		$('#modaltitle').html("Upload Raw File");
		$.post("../Modules/Document_Management/apps/upload_docs.php", { fullpath: fullpath, folder: folder, subfolder: subfolder },
		function(data) {
			$('#formmodal_page').html(data);
			$('#formmodal').show();
		});
	}
	
}
</script>