<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
$function = new FDSFunctions;
if(isset($_SESSION['FDS_REPORT_PAGE']))
{
	$report_page = $_SESSION['FDS_REPORT_PAGE'];
} else {
	$report_page = '';
}
if(isset($_SESSION['FDS_RECIPIENT_REPORT']))
{
	$recipient = $_SESSION['FDS_RECIPIENT_REPORT'];
} else {
	$recipient = 'WAREHOUSE';
}
if(isset($_SESSION['FDS_REPORT_BRANCH']))
{
	$report_branch = $_SESSION['FDS_REPORT_BRANCH'];
} else {
	$report_branch = '';
}

?>
<style>
#branch {
	display: none;
}
</style>
<div class="smnav-header">
	<input type="text" id="branch" class="form-control form-control-sm" style="width:200px" onfocus="openBranch()" placeholder="Select Branch" value="<?php echo $report_branch; ?>" readonly>
	<select id="recipient" class="form-control form-control-sm" style="width:200px" disabled>
		<?php echo $function->GetRecipient('FROZEN DOUGH',$db); ?>
	</select>
		<select id="reports" class="form-control form-control-sm" style="width:200px" onchange="showBranchInput(this.value)">
		<?php echo $function->GetFDSReports($report_page,$db); ?>
	</select>
	<button class="btn btn-primary btn-sm" onclick="loadInventory()">Load Report</button>
</div>
<div id="report_results"></div>
<script>
$(function()
{
	if('<?php echo $report_page; ?>' === 'Branch Out Deliveries')
	{
		$('#branch').show();
	} else {
		$('#branch').hide();
	}
});
function showBranchInput(report)
{
	if(report == 'Branch Out Deliveries')
	{
		$('#branch').show();
	} else {
		$('#branch').hide();
	}
}
function openBranch()
{
	$('#modaltitle').html("Select Branch");
	$.post("./Modules/Frozen_Dough_Management/fds_report/inventory_branch_select.php", { },
	function(data) {		
		$('#formmodal_page').html(data);	
		$('#formmodal').show();
	});
}
function loadInventory()
{

	const branch = '<?php echo htmlspecialchars($report_branch); ?>';
	const recipient = $('#recipient').val();
	const reports = $('#reports').val();
	
	if (!recipient) {
		swal("Recipient", "Please select a Recipient", "warning");
		return;
	}

	if (!reports) {
		swal("Report", "Please select a Report", "warning");
		$('#reports').focus();
		return;
	}


	let page = 'inventory_report.php';
	
	if(reports === 'Branch Order') {
		page = 'branch_order_receiving.php';
	} else if (reports === 'Branch Out Deliveries') {
		page = 'branch_report.php';
	} else if (reports === 'Delivery Out VS Branch Received') {
		page = 'inventory_report2.php';
	} else if (reports === 'View User Logs') {
		page = 'view_user_logs.php';
	}
		
	
	$.post(`./Modules/Frozen_Dough_Management/fds_report/${page}`, { branch, reports, recipient }, function(data) {
		$('#report_results').html(data);	
	});
	
}
</script>