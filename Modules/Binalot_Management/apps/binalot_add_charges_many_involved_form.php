<?php

include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php") ;

$function = new BINALOTFunctions;
$dateNow = $_SESSION['BINALOT_TRANSDATE'];
$user = $_SESSION['application_appnameuser'];


$reportdate = $_POST['reportdate'];
$category = $_POST['category'];
$itemdescription = $_POST['itemdescription'];
$itemcode = $_POST['itemcode'];
$quantity = $_POST['quantity'];
$unitprice = $_POST['unitprice'];
$remarks = $_POST['remarks'];

$chargecodeno = $function->generateControlNumber();

?>
<style>
.form-wrapper {width:500px;max-height:750px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr>
			<th>Charge Code</th>
			<td>
				<input id="chargecode" type="text" class="form-control" value="<?php echo $chargecodeno?>" disabled>
			</td>
		</tr>
		<tr>
			
			<th>Report Date</th>
			<td>
				<input id="dateselected" type="text" class="form-control form-control-sm" value="<?php echo $reportdate?>" disabled>
			</td>		
		</tr>
		<tr>
			<th>IDCode</th>
			<td>
				<input id="idcode" type="text" class="form-control form-control-sm" value="" disabled>
			</td>
		</tr>
		<tr>
			<th>
		        <button type="button" class="btn btn-primary btn-sm" onclick="addthisemployeecharge()">Add</button>
		        <button type="button" class="btn btn-danger btn-sm" onclick="clearthisemployeecharge()">Clear</button>
		    </th>
			
			<td>
				<input id="employeename" type="text" list="employeeList" class="form-control form-control-sm"  onkeyup="acctnameCkecking(this.value)" autocomplete="off">
				<datalist id="employeeList">
					<?php echo $function->selectEmployeeList($db); ?>
				</datalist>
			</td>
		</tr>
		
		
		<tr>
			<th>Selected Employees</th>
			<td id="">
				<div id="selectedEmployeesBox" class="form-control" style="height:100px; overflow-y:auto; font-size:xx-small">
		            <?php $function->getChargesPeaple('employee_name',$chargecodeno,$db)?>
		        </div>
			</td>
		
		</tr>
		
		
		
		<tr>
			<th>Encoded By</th>
			<td>
				<input id="encodedby" type="text" class="form-control form-control-sm" value="<?php echo $user?>" disabled>
			</td>
		</tr>

		<tr>
			<th>Category</th>
			<td>
				<input id="category" type="text" class="form-control form-control-sm" value="<?php echo $category?>" disabled>
			</td>
		</tr>
		<tr>
			<th>Item Description</th>
			<td>
				<input id="item_description" type="text" class="form-control form-control-sm" value="<?php echo $itemdescription?>" disabled>
			</td>
		</tr>
		<tr>
			<th>ITEM CODE</th>
			<td>
				<input id="itemcode" type="text" class="form-control" value="<?php echo $itemcode?>" disabled>
			</td>
		</tr>
		<tr>
			<th>Quantity</th>
			<td>
				<input id="quantity" type="text" class="form-control form-control-sm" value="<?php echo $quantity?>" disabled>
			</td>
		</tr>
		<tr>
			<th>Unit Price</th>
			<td>
				<input id="unitprice" type="text" class="form-control form-control-sm" value="<?php echo $unitprice?>" disabled>
			</td>
		</tr>
		<tr>
			<th>Remarks</th>
	    	<td>
		        <textarea id="remarks" class="form-control form-control-sm" rows="3" disabled="disabled"><?php echo $remarks?></textarea>
		    </td>
		</tr>


	</table>
	<div style="float:left">
		<span style="font-size:small">Click <a href="#" onclick="manyinvolvedcharge()"><i class="fa fa-undo" aria-hidden="true"></i></a></span>
	</div>
	<div style="float:right; height: 26px;">
		<button type="button" class="btn btn-success btn-sm" onclick="savethisData()">Save Charges</button>
	</div>
</div>
<div id="resultsCharges"></div>
<script>
function clearthisemployeecharge(){
	
	var mode = 'deletethisemployeechargeextension';
	var chargecode = $('#chargecode').val();

	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, chargecode: chargecode },
    function(data) {
        $("#selectedEmployeesBox").html(data);
    });


}


function addthisemployeecharge(){
	
	var mode = 'addthisemployeechargeextension';
	var dateselected = $('#dateselected').val();
	var chargecode = $('#chargecode').val();
	var idcode = $('#idcode').val();
	var employeename = $('#employeename').val();
	var encodedby = $('#encodedby').val();
	var category = $('#category').val();
	var itemdescription = $('#item_description').val();
	var itemcode = $('#itemcode').val();
	var quantity = $('#quantity').val();
	var unitprice = $('#unitprice').val();
	var remarks = $('#remarks').val();
	
	if (employeename=='') {
        	app_alert("System Message", "No Employee is selected", "warning");
        return;
    }
	if (idcode=='' || encodedby=='' || encodedby=='' || category=='' || itemdescription=='' || itemcode=='' || quantity=='') {
        	app_alert("System Message", "Something is missing in the data, it cannot proceed", "warning");
        return;
    }
    
	$.post("./Modules/Binalot_Management/actions/actions.php", {
																mode: mode, 
																dateselected: dateselected,
																chargecode: chargecode,
																idcode: idcode,
																employeename: employeename,
																encodedby: encodedby,
																category: category,
																itemdescription: itemdescription,
																itemcode: itemcode,
																quantity: quantity,
																unitprice: unitprice,
																remarks: remarks
															},
    function(data) {
        $("#selectedEmployeesBox").html(data);
    });

}

function manyinvolvedcharge(){
	$('#modaltitle').html("ADD NEW CHARGES DATA");
	$.post("./Modules/Binalot_Management/apps/binalot_add_charges_many_involved.php", { },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function acctnameCkecking(params){
	
	var mode = 'acctnameCkecking';	
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, employeename: params },
    function(data) {
        $("#idcode").html(data);
    });
}

function savethisData(){
    var mode = 'saveChargesInvolved';

    var chargecode = $('#chargecode').val();
    var reportDate = $('#dateselected').val();
    var encodedBy = $('#encodedby').val();
    var category = $('#category').val();
    var itemDescription = $('#item_description').val();
    var itemCode = $('#itemcode').val();
    var quantity = $('#quantity').val();
    var unitprice = $('#unitprice').val();
    var remarks = $('#remarks').val();

	var selectedemployee = $('#selectedEmployeesBox').text().trim();	
	
	if(selectedemployee == ''){
		app_alert("System Message", "No Employee is selected", "warning");
		return false
	}
	
	rms_reloaderOn("Generating...");
	
	
    $.post("./Modules/Binalot_Management/actions/actions.php",  
        { 
            mode: mode, 
            chargecode: chargecode,
            reportDate: reportDate,
            encodedBy: encodedBy,
            category: category,
            itemDescription: itemDescription,
            itemCode: itemCode,
            quantity: quantity,
            unitprice: unitprice,
            remarks: remarks
        },
        function(data) {
            $("#smnavdata").html(data);
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





function generateControlNumber() {
    // Get current date and time components
    const date = new Date();
    const year = date.getFullYear().toString().slice(-2);  // Last two digits of year
    const month = ('0' + (date.getMonth() + 1)).slice(-2); // Month with leading zero
    const day = ('0' + date.getDate()).slice(-2);          // Day with leading zero
    const hours = ('0' + date.getHours()).slice(-2);       // Hours with leading zero
    const minutes = ('0' + date.getMinutes()).slice(-2);   // Minutes with leading zero
    const seconds = ('0' + date.getSeconds()).slice(-2);   // Seconds with leading zero

    // Generate a random 4-digit number
    const randomNum = Math.floor(1000 + Math.random() * 9000);

    // Combine everything to create the control number
    const controlNumber = `${year}${month}${day}${hours}${minutes}${seconds}${randomNum}`;
    return controlNumber;
}



</script>

