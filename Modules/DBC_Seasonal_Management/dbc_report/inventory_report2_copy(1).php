<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;
$_SESSION['DBC_SEASONAL_REPORT_PAGE'] = $_POST['reports'];
$_SESSION['DBC_SEASONAL_RECIPIENT_REPORT'] = $_POST['recipient'];
$_SESSION['DBC_SEASONAL_BRANCH_REPORT'] = $_POST['branch'];

$selectedcluster = @$_SESSION['DBC_SEASONAL_SUMMARY_SELECTEDCLUSTER'];

$selecteddate = @$_SESSION['DBC_SUMMARY_SELECTEDDATED'];
/*
$datefrom = @$_SESSION['DBC_SEASONAL_SUMMARY_DATEFROM'];
$dateto = @$_SESSION['DBC_SEASONAL_SUMMARY_DATETO'];
*/
?>
<style>
.subpage-wrapper {margin-top:5px;border:1px solid #aeaeae;background:#fff;}
.tableFixHead {margin-top:5px;background:#fff;}
.tableFixHead  { overflow: auto; height: calc(100vh - 272px); width:100% }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; background:green; color:#fff }
.tableFixHead table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHead td { font-size:14px; white-space:nowrap } 
.subpage-wrapper {display: flex;gap: 5px;white-space:nowrap;border:1px solid #aeaeae;border-bottom: 3px solid #aeaeae;padding:10px;
background: #fff;min-width:600px;overflow-x:auto;}
</style>
<div class="subpage-wrapper">
	

	<input id="selectedcluster" type="text" list="selectedbranchList" style="width:260px" class="form-control form-control-sm"  placeholder="---Select Cluster---" value="<?php echo @$selectedcluster?>" autocomplete="off">
	<datalist id="selectedbranchList">
		<?php echo $function->GetCluster($selectedcluster,$db)?>
	</datalist>

	<!--	
	<input type="date" id="datefrom" class="form-control form-control-sm" style="width:150px" placeholder="Date from" value="<?php echo @$datefrom?>">
	<input type="date" id="dateto" class="form-control form-control-sm" style="width:150px" placeholder="Date to" value="<?php echo @$dateto?>">
	-->
	
	<input type="date" id="selecteddate" class="form-control form-control-sm" style="width:150px" placeholder="Date from" value="<?php echo @$selecteddate?>">

	
	<button id="searchbtn" class="btn btn-info btn-sm" onclick="searchItem()">Search</button>
	
</div>
<div class="tableFixHead" id="iwd"></div>
<script>



function searchItem() {

   

	var selectedcluster = $('#selectedcluster').val();
    var selecteddate = $('#selecteddate').val();

    var selectedclusterInput = document.getElementById("selectedcluster");
    var selecteddateInput = document.getElementById("selecteddate");

    // Reset input borders
    selectedclusterInput.style.border = "";
    selecteddateInput.style.border = "";
        
    // Validate cluster selection
    if (selectedcluster === "" || selectedcluster == null) {
        selectedclusterInput.style.border = "2px solid red";
        app_alert("System Message", "Please select cluster.", "warning");
        return;
    }

    // Validate date selection
    if (selecteddate === "" || selecteddate == null) {
        selecteddateInput.style.border = "2px solid red";
        app_alert("System Message", "Date is not valid", "warning");
        return;
    }

    // Convert selected date to Date object and check for validity
    var selecteddateObj = new Date(selecteddate);

    if (isNaN(selecteddateObj.getTime())) {
        selecteddateInput.style.border = "2px solid red";
        app_alert("System Message", "Please enter a valid date.", "warning");
        return;
    }

    var page = 'deliveryout_vs_branchreceiving.php';
	rms_reloaderOn('Searching...');
	$.post("./Modules/DBC_Seasonal_Management/dbc_report/" + page, { selectedcluster: selectedcluster, selecteddate: selecteddate},
	function(data) {		
		$('#iwd').html(data);
		rms_reloaderOff();
	});

}






/*
function loadReport()
{
	var recipient = $('#recipient').val();
	var year = $('#year').val();
	var month = $('#month').val();
	var week = $('#week').val();
	var page = 'monthly_inventory_report_data.php';
	
	rms_reloaderOn('Loading');
	setTimeout(function()
	{
		$.post("./Modules/DBC_Seasonal_Management/dbc_report/" + page, { recipient: recipient, year: year, month: month, week: week },
		function(data) {		
			$('#iwd').html(data);
			rms_reloaderOff();
		});
	},500);
}
*/
</script>

