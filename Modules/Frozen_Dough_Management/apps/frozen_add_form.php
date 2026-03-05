<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php") ;

$function = new FDSFunctions;
$dateNow = $_SESSION['FDS_TRANSDATE'];
$user = $_SESSION['application_appnameuser'];
?>
<style>
.form-wrapper {width:500px;max-height:500px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr>
			<th>Report Date</th>
			<td>
				<input id="dateselected" type="text" class="form-control" value="<?php echo $dateNow; ?>" disabled>
			</td>		
		</tr>
		<tr>
			<th>Supplier</th>
			<td>
				<input id="supplier" type="text" class="form-control" value="INDANGAN FROZEN DOUGH" disabled>
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
			<th>ITEM CODE</th>
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
	<div style="float:right">
		<button type="button" class="btn btn-success btn-sm" onclick="saveFgts()">Add FGTS</button>
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
	
    $.post("./Modules/Frozen_Dough_Management/actions/actions.php",  
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
	
	$.post("./Modules/Frozen_Dough_Management/actions/actions.php",  { mode: mode, batch: batch, itemcode: itemcode },
	function(data) {
		$("#results").html(data);
	});
}
function getItemCode(){
	var mode = 'getItemCode';
	var itemname = $('#item_description').val();
	
	$.post("./Modules/Frozen_Dough_Management/actions/actions.php",  { mode: mode, itemname: itemname },
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

