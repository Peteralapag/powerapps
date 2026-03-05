<?php
include '../../../init.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT'] . "/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;

$report_page   = $_SESSION['DBC_REPORT_PAGE']   ?? '';
$recipient     = $_SESSION['DBC_RECIPIENT_REPORT'] ?? 'WAREHOUSE';
$report_branch = $_SESSION['DBC_REPORT_BRANCH'] ?? '';
?>
<style>
#branch { display: none; }
.smnav-header { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
</style>

<div class="smnav-header">
	<input 
		type="text" 
		id="branch" 
		class="form-control form-control-sm" 
		style="width:200px" 
		onfocus="openBranch()" 
		placeholder="Select Branch" 
		value="<?= htmlspecialchars($report_branch) ?>" 
		readonly
	>
	
	<select id="recipient" class="form-control form-control-sm" style="width:200px" disabled>
		<?= $function->GetRecipient('DAVAO BAKING CENTER', $db) ?>
	</select>
	
	<select id="reports" class="form-control form-control-sm" style="width:200px">
		<?= $function->GetDBCReports($report_page, $db) ?>
	</select>

	<button id="btnLoadReport" class="btn btn-primary btn-sm">Load Report</button>
</div>

<div id="report_results"></div>

<script>
$(function() {
	const $branch = $('#branch');
	const $reports = $('#reports');
	const $recipient = $('#recipient');
	const $reportResults = $('#report_results');

	// Initial state
	toggleBranchInput($reports.val());

	// When user changes report type
	$reports.on('change', function() {
		toggleBranchInput(this.value);
		$reportResults.empty();
	});

	// Faster load with async handler
	$('#btnLoadReport').on('click', loadInventory);

	function toggleBranchInput(report) {
		$branch.toggle(report === 'Branch Out Deliveries');
	}

	function openBranch() {
		$('#modaltitle').text("Select Branch");
		$('#formmodal_page').load("./Modules/DBC_Management/dbc_report/inventory_branch_select.php", function() {
			$('#formmodal').show();
		});
	}

	function loadInventory() {
		const branch = "<?= htmlspecialchars($report_branch) ?>";
		const recipient = $recipient.val();
		const reportType = $reports.val();

		if (!recipient) return swal("Recipient", "Please select a Recipient", "warning");
		if (!reportType) return swal("Report", "Please select a Report", "warning");

		const pages = {
			'Branch Order': 'branch_order_receiving.php',
			'Branch Out Deliveries': 'branch_report.php',
			'Delivery Out VS Branch Received': 'inventory_report2.php',
			'DOBR via Branch': 'dobr_via_branch.php',
			'View User Logs': 'view_user_logs.php'
		};

		const page = pages[reportType] || 'inventory_report.php';

		// Add loading indicator for UX
		$reportResults.html('<div class="text-center p-3">Loading report...</div>');

		// AJAX post request
		$.post(`./Modules/DBC_Management/dbc_report/${page}`, { branch, reports: reportType, recipient })
			.done(data => $reportResults.html(data))
			.fail(() => swal("Error", "Failed to load report. Please try again.", "error"));
	}
});
</script>
