<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php") ;

$function = new DBCFunctions;
$dateNow = $_SESSION['DBC_TRANSDATE'];
$user = $_SESSION['application_appnameuser'];
?>
<style>
.fgts-form-shell {
	width:560px;
	max-height:72vh;
	overflow-y:auto;
	background:#fff;
	border:1px solid #dee2e6;
	border-radius:10px;
	box-shadow:0 2px 8px rgba(0,0,0,0.06);
	padding:12px;
}
.fgts-title {
	font-size:15px;
	font-weight:700;
	color:#2f3b4a;
	margin-bottom:8px;
}
.fgts-subtitle {
	font-size:12px;
	color:#6c757d;
	margin-bottom:10px;
}
.fgts-form-table {
	margin-bottom:0;
}
.fgts-form-table th {
	font-size:13px !important;
	font-weight:600;
	color:#3e4a59;
	width:155px;
	padding:6px 8px;
	vertical-align:middle;
}
.fgts-form-table td {
	padding:6px 8px;
	vertical-align:middle;
}
.fgts-form-table .form-control {
	border-radius:6px;
	font-size:13px;
	height:34px;
}
.fgts-form-table .form-control:disabled {
	background:#f8f9fa;
	color:#495057;
}
.fgts-actions {
	display:flex;
	justify-content:flex-end;
	margin-top:12px;
	padding-top:10px;
	border-top:1px solid #e9ecef;
}
.fgts-actions .btn {
	min-width:120px;
	font-size:12px;
	font-weight:600;
	padding:6px 12px;
}
</style>
<div class="fgts-form-shell">	
	<div class="fgts-title">FGTS Entry Form</div>
	<div class="fgts-subtitle">Create daily FGTS record for DBC production receiving.</div>
	<table style="width: 100%" class="table table-borderless fgts-form-table">
		<tr>
			<th>Report Date</th>
			<td>
				<input id="dateselected" type="text" class="form-control" value="<?php echo $dateNow; ?>" disabled>
			</td>		
		</tr>
		<tr>
			<th>Supplier</th>
			<td>
				<input id="supplier" type="text" class="form-control" value="DAVAO BAKING CENTER" disabled>
			</td>
		</tr>
		<tr>
			<th>Encoded By</th>
			<td>
				<input id="encodedby" type="text" class="form-control" value="<?php echo $user?>" disabled>
			</td>
		</tr>

		<tr>
			<th>Category</th>
			<td>
				<input id="category" type="text" class="form-control" value="" disabled>
			</td>
		</tr>
		<tr>
			<th>Item Description</th>
			<td>
				<select id="item_description" class="form-control" onchange="getItemCode()">
					<?php echo $function->GetItemDescription($db)?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Item Code</th>
			<td>
				<input id="itemcode" type="text" class="form-control" value="" disabled>
			</td>
		</tr>
		<tr>
			<th>Batch</th>
			<td>
				<input id="batch" type="number" class="form-control" value="" onkeyup="getActualYield()">
			</td>
		</tr>
		<tr>
			<th>Baker's Yield</th>
			<td>
				<input id="actualyield" type="text" class="form-control" value="" disabled>
			</td>
		</tr>
		<tr>
			<th>Time Created</th>
			<td>
				<input type="time" id="timecreated" class="form-control">
			</td>
		</tr>
	</table>
	<div class="fgts-actions">
		<button type="button" class="btn btn-success btn-sm" onclick="saveFgts()">Save FGTS</button>
	</div>
</div>
<div id="results"></div>
<script>


function saveFgts(){
    var mode = 'saveFgts';

    var reportDate = $('#dateselected').val();
    var supplier = $('#supplier').val();
    var encodedBy = $('#encodedby').val();
    var category = $('#category').val();
    var itemDescription = $('#item_description').val();
    var itemCode = $('#itemcode').val();
    var batch = $('#batch').val();
    var actualYield = $('#actualyield').val();
    var timecreated = $('#timecreated').val();
    
    if (!isValidDate(reportDate)) {
        	app_alert("System Message", "Please select a valid date", "warning");
        return;
    }
	if (itemDescription=='') {
        	app_alert("System Message", "Please select Item", "warning");
        return;
    }
	if (batch=='') {
        	app_alert("System Message", "Batch is empty", "warning");
        return;
    }
	if (timecreated=='') {
        	app_alert("System Message", "Time is empty", "warning");
        return;
    }

	rms_reloaderOn("Generating...");
	
    $.post("./Modules/DBC_Management/actions/actions.php",  
        { 
            mode: mode, 
            reportDate: reportDate,
            supplier: supplier,
            encodedBy: encodedBy,
            category: category,
            itemDescription: itemDescription,
            itemCode: itemCode,
            batch: batch,
            actualYield: actualYield,
            timecreated: timecreated
        },
        function(data) {
            $("#results").html(data);
            rms_reloaderOff();
        }
    );
}

function isValidDate(dateString) {
    var regex = /^\d{4}-\d{2}-\d{2}$/;
    if(!regex.test(dateString)) {
        return false;
    }

    var date = new Date(dateString);
    if(isNaN(date.getTime())) {
        return false;
    }

    return true;
}
function getActualYield(){
	var mode = 'getActualYield';
	var itemcode = $('#itemcode').val();
	var batch = $('#batch').val();
	
	$.post("./Modules/DBC_Management/actions/actions.php",  { mode: mode, batch: batch, itemcode: itemcode },
	function(data) {
		$("#results").html(data);
	});
}
function getItemCode(){
	var mode = 'getItemCode';
	var itemname = $('#item_description').val();
	
	$.post("./Modules/DBC_Management/actions/actions.php",  { mode: mode, itemname: itemname },
	function(data) {
		$("#results").html(data);
	});
}

$(function()
{
	$("#dateselected").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
});
</script>

