<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php") ;

$function = new DBCFunctions;
$dateNow = $_SESSION['DBC_SEASONAL_TRANSDATE'];
$user = $_SESSION['application_appnameuser'];


$chargecodeno = $_POST['chargecodeno'];
$itemname = $_POST['itemname'];
$itemcode = $_POST['itemcode'];
$category = $_POST['category'];
$original_qty = $_POST['original_qty'];
$original_unitprice = $_POST['original_unitprice'];
$original_total = $_POST['original_total'];

$remainingqty = (float)$function->countDetectEmployeeChargedBAKERONLY2('quantity', $chargecodeno, $db);
$remainingtotal = (float)$function->countDetectEmployeeChargedBAKERONLY2('total', $chargecodeno, $db);
?>
<style>
.form-wrapper {width:500px;max-height:600px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr>
			<th>Remaining Quantity</th>
			<td>
				<input id="remainingqty" type="text" class="form-control" value="<?php echo $remainingqty?>" disabled>
				<input id="remainingtotal" type="hidden" class="form-control" value="<?php echo $remainingtotal?>" disabled>
			</td>		
		</tr>
		
		<tr>
			<th>Report Date</th>
			<td>
				<input id="dateselected" type="text" class="form-control" value="<?php echo $dateNow; ?>" disabled>
			</td>		
		</tr>

		<tr>
			<th>I.D Code</th>
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
				<input id="category" type="text" class="form-control" value="<?php echo $category?>" disabled>
			</td>
		</tr>
		<tr>
			<th>Item Description</th>
			<td>
				<input id="itemname" type="text" class="form-control" value="<?php echo $itemname?>" disabled>
			</td>
		</tr>
		<tr>
			<th>ITEM CODE</th>
			<td>
				<input id="itemcode" type="text" class="form-control" value="<?php echo $itemcode?>" disabled>
			</td>
		</tr>
		<tr>
			<th>Unit Price</th>
			<td>
				<input id="unitprice" type="number" class="form-control" value="" onkeyup="calculateTotal()" autocomplete="off">
			</td>
		</tr>

		<tr>
			<th>Quantity</th>
			<td>
				<input id="quantity" type="number" class="form-control" value="" onkeyup="calculateTotal()" autocomplete="off">
			</td>
		</tr>
		<tr>
			<th>Total</th>
			<td>
				<input id="total" type="number" class="form-control" disabled>
			</td>
		</tr>


	</table>
	<div style="float:right">
		<button type="button" class="btn btn-success btn-sm" onclick="savethisEmployeeCharge()">Save Employee Charges</button>
	</div>
</div>
<div id="results"></div>
<script>
function acctnameCkecking(params){
	
	var mode = 'acctnameCkecking';	
	$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, employeename: params },
    function(data) {
        $("#idcode").html(data);
    });
}

function savethisEmployeeCharge(){
    var mode = 'saveChargesEmployee';
	
	var chargecodeno = '<?php echo $chargecodeno?>';
	
	var remainingqty = parseFloat($('#remainingqty').val());
	var remainingtotal = parseFloat($('#remainingtotal').val());
	
	
	var dateselected = $('#dateselected').val();
    var idcode = $('#idcode').val();
    var employeename = $('#employeename').val();
    var encodedby = $('#encodedby').val();
    var category = $('#category').val();
    var itemname = $('#itemname').val();
    var itemcode = $('#itemcode').val();
    var unitprice = parseFloat($('#unitprice').val());
    var quantity = parseFloat($('#quantity').val());
    var total = parseFloat($('#total').val());	
	
	var original_unitprice = <?php echo $original_unitprice?>;
    var original_quantity = <?php echo $original_qty?>;
	var original_total = <?php echo $original_total?>;
	
   

    if (employeename=='') {
        	app_alert("System Message", "Employee Name not exist", "warning");
        return;
    }
    if (idcode=='') {
        	app_alert("System Message", "No IDCODE Found", "warning");
        return;
    }
	if (encodedby=='') {
        	app_alert("System Message", "Encoded By not exist", "warning");
        return;
    }
	if (category=='') {
        	app_alert("System Message", "Category not exist", "warning");
        return;
    }
	if (itemname=='') {
        	app_alert("System Message", "Please select Item", "warning");
        return;
    }
	if (itemcode=='') {
        	app_alert("System Message", "Please select Item Code", "warning");
        return;
    }
    if (unitprice=='') {
        	app_alert("System Message", "Please select Unit Price", "warning");
        return;
    }
    if (quantity=='') {
        	app_alert("System Message", "Please select quantity", "warning");
        return;
    }
    
    
    if (unitprice > original_unitprice) {
        	app_alert("System Message", "Unit Price is not higher than original price", "warning");
        return;
    }

/*	
	if (quantity > remainingqty) {
        	app_alert("System Message", "Quantity is higher than remaining quantity"+ quantity+ " > " +remainingqty, "warning");
        return;
    }
*/
	
    
	rms_reloaderOn("Generating...");
	
    $.post("./Modules/DBC_Seasonal_Management/actions/actions.php",  
        { 
            mode: mode,
            chargecodeno: chargecodeno,
            dateselected: dateselected,
            idcode: idcode,
            employeename: employeename,
            encodedby: encodedby,
            category: category,
            itemname: itemname,
            itemcode: itemcode,
            unitprice: unitprice,
            quantity: quantity,
            total: total,
            original_unitprice: original_unitprice,
            original_quantity: original_quantity,
            original_total: original_total,
            remainingqty: remainingqty,
            remainingtotal: remainingtotal
        },
        function(data) {
            $("#results").html(data);
            rms_reloaderOff();
        }
    );
}


function getItemCode(){
	var mode = 'getItemCodeNewModules';
	var itemname = $('#item_description').val();
	
	$.post("./Modules/DBC_Seasonal_Management/actions/actions.php",  { mode: mode, itemname: itemname },
	function(data) {
		$("#results").html(data);
	});
}

function calculateTotal() {
    var unitprice = parseFloat(document.getElementById('unitprice').value) || 0;
    var quantity = parseFloat(document.getElementById('quantity').value) || 0;
    var total = unitprice * quantity;

    document.getElementById('total').value = total.toFixed(2);
}
</script>

