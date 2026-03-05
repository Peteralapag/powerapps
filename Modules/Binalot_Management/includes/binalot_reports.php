<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;

$report_page = $_SESSION['BINALOT_REPORT_PAGE'] ?? '';
$recipient = $_SESSION['BINALOT_RECIPIENT_REPORT'] ?? 'WAREHOUSE';
$report_branch = $_SESSION['BINALOT_REPORT_BRANCH'] ?? '';

?>
<style>
#branch {
	display: none;
}
</style>

<div class="smnav-header">
	<input type="text" id="branch" class="form-control form-control-sm" style="width:200px" onfocus="openBranch()" placeholder="Select Branch" value="<?php echo htmlspecialchars($report_branch); ?>" readonly>
	
	<select id="recipient" class="form-control form-control-sm" style="width:200px" disabled>
		<?php echo $function->GetRecipient('BINALOT', $db); ?>
	</select>
	
	<select id="reports" class="form-control form-control-sm" style="width:200px" onchange="toggleBranchInput(this.value)">
		<?php echo $function->GetBINALOTReports($report_page, $db); ?>
	</select>
	<button class="btn btn-primary btn-sm" onclick="loadInventory()">Load Report</button>
</div>

<div id="report_results"></div>

<script>
$(document).ready(function() {
	const reportPage = '<?php echo $report_page; ?>';

	toggleBranchInput(reportPage);
});

function toggleBranchInput(report) {
	if (report === 'Branch Out Deliveries') {
		$('#branch').show();
	} else {
		$('#branch').hide();
	}
	$('#report_results').html('');
}

function openBranch() {
	$('#modaltitle').html("Select Branch");
	$.post("./Modules/Binalot_Management/binalot_report/inventory_branch_select.php", {}, function(data) {
		$('#formmodal_page').html(data);	
		$('#formmodal').show();
	});
}

function loadInventory() {
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

		
	
	$.post(`./Modules/Binalot_Management/binalot_report/${page}`, { branch, reports, recipient }, function(data) {
		$('#report_results').html(data);	
	});
}
</script>
