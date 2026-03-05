<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['md_username'];
$date_now = date("Y-m-d H:i:s");
$filename = $_POST['filename'];
$application = $_POST['application'];
$module = $_POST['module'];
$account_name = $_POST['account_name'];
$requested_by = $_POST['requested_by'];
?>
<style>
.request-form {
	width: 500px;
	font-size:14px;
}
.request-form textarea {
	width: 100% !important;
	height: 80px !important;
}
</style>
<div class="request-form">
	<table style="width: 100%">
		<tr>
			<td style="width: 123px">REQUESTED BY</td>
			<td>
				<input id="requestedby" type="text" class="form-control" value="<?php echo $requested_by; ?>" disabled>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td style="width: 123px">FILE NAME</td>
			<td><input id="filename" type="text" class="form-control" value="<?php echo $filename; ?>" disabled></td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td style="width: 123px">REQUEST TYPE</td>
			<td>
				<select id="rtype" class="form-control" onchange="checkRequestType('<?php echo $username; ?>','<?php echo $filename; ?>',this.value)">
					<option value="">--- Select Type ---</option>
					<option value="1">Print / Download</option>
					<option value="2">Rename File</option>
					<option value="3">Delete File</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td style="width: 123px">NOTE</td>
			<td>
				<textarea id="reason" class="form-control" placeholder="Reason (Required)"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"><hr></td>			
		</tr>
		<tr>
			<td colspan="2" style="height:10px;text-align:right">
				<button class="btn btn-primary btn-sm btnsubmit" onclick="submitRequest()">Submit Request</button>
				<button class="btn btn-danger btn-sm btnsubmit" onclick="closeModal('formmodal')">Cancel</button>
			</td>			
		</tr>
	</table>
</div>
<div id="requestresults"></div>
<script>
function checkRequestType(username,filename,rtype)
{
	var mode = 'checkrequesttype';
	$.post('../../../Modules/Document_Management/actions/actions.php', { mode: mode, username: username, rtype: rtype, filename: filename },
	function(data) {
		$('#requestresults').html(data);
	});
}
function submitRequest()
{
	var mode = 'submitrequest';
	var application = '<?php echo $application; ?>';
	var module = '<?php echo $module; ?>';
	var file_name = $('#filename').val();
	var account_name = '<?php echo $account_name; ?>';
	var requested_by = $('#requestedby').val();
	var request_reason = $('#reason').val();
	var type = $('#rtype').val();

	if(type == '')
	{
		swal("Request Type", "Please select Request Type", "warning");
		return false;		
	}	
	rms_reloaderOn("Saving Request...");
	setTimeout(function()
	{
		$.post('../../../Modules/Document_Management/actions/actions.php', { 
			mode: mode,
			application: application,
			module: module,
			file_name: file_name,
			account_name: account_name,
			requested_by: requested_by,
			request_reason: request_reason,
			type: type,
		},
		function(data) {
			$('#requestresults').html(data);	
			rms_reloaderOff();
		});
	},1000);

}
</script>
