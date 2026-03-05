<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	

$rowid = $_POST['rowid'];
$sql = "SELECT applications, modules, file_name, requested_by FROM tbl_app_request WHERE id='$rowid'";
$result = $db->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
    	$applications = $row['applications'];
    	$modules = $row['modules'];
    	$file_name = $row['file_name'];
    	$requested_by = $row['requested_by'];
	}
} 
else {
  	$applications = '';
	$modules = '';
	$file_name = '';
	$requested_by = '';
}

?>
<style>
.request-form td {
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
			<td style="width: 150px; height: 30px;">REQUESTED BY</td>
			<td style="height: 30px">
				<input id="accountname" type="text" class="form-control form-control-sm" value="<?php echo $requested_by?>" disabled>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td style="width: 150px; height: 26px;">APPLICATION</td>
			<td style="height: 26px"><input id="filename" type="text" class="form-control form-control-sm" value="<?php echo $applications?>" disabled></td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td style="width: 150px; height: 26px;">MODULE</td>
			<td style="height: 26px"><input id="filename" type="text" class="form-control form-control-sm" value="<?php echo $modules?>" disabled></td>
		</tr>

		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td style="width: 150px; height: 26px;">FILE NAME</td>
			<td style="height: 26px"><input id="filename" type="text" class="form-control form-control-sm" value="<?php echo $file_name; ?>" disabled></td>
		</tr>

		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td colspan="2" style="height:10px"></td>			
		</tr>
		<tr>
			<td style="width: 150px">REJECTION REASON</td>
			<td>
				<textarea id="reason" class="form-control form-control-sm" placeholder="Reason of rejection (Required)"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"><hr></td>			
		</tr>
		<tr>
			<td colspan="2" style="height:10px;text-align:right">
				<button class="btn btn-primary btn-sm btnsubmit" onclick="submitRejection('<?php echo $rowid?>')">Reject Request</button>
				<button class="btn btn-danger btn-sm btnsubmit" onclick="showRequest()">Cancel</button>
			</td>			
		</tr>
	</table>
</div>
<div id="actionresults"></div>

<script>
function submitRejection(rowid){
	var column = 'approved';
	var value = '2';
	var reasonRejection = $('#reason').val();
	
	if(reasonRejection == ''){
		app_alert('Warning','Please Fill Rejection Reason','warning');
		return false;
	}
	
	$.post("../Actions/request_actions.php", { rowid: rowid, column: column, value: value, reasonRejection: reasonRejection  },
	function(data) {
		$('#actionresults').html(data);
		app_alert('Success','Successfully Rejected','success');
		showRequest();
	});
}
</script>