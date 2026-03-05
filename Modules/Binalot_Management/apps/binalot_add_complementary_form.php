<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php") ;

$function = new BINALOTFunctions;
$dateNow = $_SESSION['BINALOT_TRANSDATE'];
$user = $_SESSION['application_appnameuser'];
?>
<style>
.form-wrapper {width:500px;max-height:500px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr>
			<th>Complementary Date</th>
			<td>
				<input id="dateselected" type="text" class="form-control" value="<?php echo $dateNow; ?>" disabled>
			</td>		
		</tr>
		<tr>
			<th>Branch</th>
			<td>
				<select id="branch" class="form-control" >
					<?php echo $function->GetBranch($branch,$db)?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Received By</th>
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
			<th>Quantity</th>
			<td>
				<input id="actualyield" type="number" class="form-control">
			</td>
		</tr>
		<tr>
			<th>Remarks</th>
	    	<td>
		        <textarea id="remarks" class="form-control" rows="3"></textarea>
		    </td>
		</tr>


	</table>
	<div style="float:right">
		<button type="button" class="btn btn-success btn-sm" onclick="savecomplementary()">
		Add Complementary</button>
	</div>
</div>
<div id="results"></div>
<script>


function savecomplementary(){
    var mode = 'saveComplementary';

    var reportDate = $('#dateselected').val();
    var branch = $('#branch').val();
    var encodedBy = $('#encodedby').val();
    var category = $('#category').val();
    var itemDescription = $('#item_description').val();
    var itemCode = $('#itemcode').val();
    var actualYield = $('#actualyield').val();
    var remarks = $('#remarks').val();

    
    if (!isValidDate(reportDate)) {
        	app_alert("System Message", "Please select a valid date", "warning");
        return;
    }
	if (itemDescription=='') {
        	app_alert("System Message", "Please select Item", "warning");
        return;
    }
	if (branch=='') {
        	app_alert("System Message", "Please select Branch", "warning");
        return;
    }
    if (actualYield=='') {
        	app_alert("System Message", "Please select quantity", "warning");
        return;
    }
	if (remarks.trim() === '') {
	    app_alert("System Message", "Please enter remarks", "warning");
	    return;
	}

	rms_reloaderOn("Generating...");
	
    $.post("./Modules/Binalot_Management/actions/actions.php",  
        { 
            mode: mode, 
            reportDate: reportDate,
            branch: branch,
            encodedBy: encodedBy,
            category: category,
            itemDescription: itemDescription,
            itemCode: itemCode,
            actualYield: actualYield,
            remarks: remarks
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
	
	$.post("./Modules/Binalot_Management/actions/actions.php",  { mode: mode, batch: batch, itemcode: itemcode },
	function(data) {
		$("#results").html(data);
	});
}
function getItemCode(){
	var mode = 'getItemCodeNewModules';
	var itemname = $('#item_description').val();
	
	$.post("./Modules/Binalot_Management/actions/actions.php",  { mode: mode, itemname: itemname },
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

