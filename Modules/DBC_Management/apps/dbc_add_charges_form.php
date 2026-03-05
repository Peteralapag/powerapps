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
.form-wrapper {width:500px;max-height:600px;overflow-y:auto;}
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
			<th>IDCode</th>
			<td>
				<input id="idcode" type="text" class="form-control" value="" disabled>
			</td>
		</tr>
		<tr>
			<th>Employee name</th>
			<td>
				<input id="employeename" type="text" list="employeeList" class="form-control"  onkeyup="acctnameCkecking(this.value)" autocomplete="off">
				<datalist id="employeeList">
					<?php echo $function->selectEmployeeList($db); ?>
				</datalist>
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
	<div style="float:left">
		<span style="font-size:small">Click <a href="#" onclick="manyinvolvedcharge()">here</a> if many involved charges</span>
	</div>
	<div style="float:right">
		<button type="button" class="btn btn-success btn-sm" onclick="savethisData()">
		Save Charges</button>
	</div>
</div>
<div id="results"></div>
<script>
function manyinvolvedcharge(){
	$('#modaltitle').html("ADD NEW CHARGES MANY INVOLVED DATA");
	$.post("./Modules/DBC_Management/apps/dbc_add_charges_many_involved.php", { },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function acctnameCkecking(params){
	
	var mode = 'acctnameCkecking';	
	$.post("./Modules/DBC_Management/actions/actions.php", { mode: mode, employeename: params },
    function(data) {
        $("#idcode").html(data);
    });
}

function savethisData(){
    var mode = 'saveCharges';

    var reportDate = $('#dateselected').val();
    var idcode = $('#idcode').val();
    var employeename = $('#employeename').val();
    var encodedBy = $('#encodedby').val();
    var category = $('#category').val();
    var itemDescription = $('#item_description').val();
    var itemCode = $('#itemcode').val();
    var actualYield = $('#actualyield').val();
    var remarks = $('#remarks').val();

    
    if (idcode=='') {
        	app_alert("System Message", "Please select valid employee name", "warning");
        return;
    }

    
    if (!isValidDate(reportDate)) {
        	app_alert("System Message", "Please select a valid date", "warning");
        return;
    }
	if (itemDescription=='') {
        	app_alert("System Message", "Please select Item", "warning");
        return;
    }
    if (actualYield=='') {
        	app_alert("System Message", "Please select quantity", "warning");
        return;
    }
	if (employeename=='') {
        	app_alert("System Message", "Employee Name not exist", "warning");
        return;
    }
    if (remarks.trim() === '') {
	    app_alert("System Message", "Please enter remarks", "warning");
	    return;
	}

    
	rms_reloaderOn("Generating...");
	
    $.post("./Modules/DBC_Management/actions/actions.php",  
        { 
            mode: mode, 
            reportDate: reportDate,
            idcode: idcode,
            employeename: employeename,
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
	
	$.post("./Modules/DBC_Management/actions/actions.php",  { mode: mode, batch: batch, itemcode: itemcode },
	function(data) {
		$("#results").html(data);
	});
}
function getItemCode(){
	var mode = 'getItemCodeNewModules';
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

