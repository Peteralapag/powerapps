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
			<th>Category</th>
			<td>
				<input id="category" type="text" class="form-control" value="" disabled>
			</td>
		</tr>
		<tr>
			<th>Item Description</th>
			<td>
				<select id="item_description" class="form-control" onchange="getItemCodecharges()">
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
				<input id="quantity" type="number" class="form-control" autocomplete="off">
			</td>
		</tr>
		<tr style="display:none">
			<th>Price</th>
			<td>
				<input id="unitprice" type="text" class="form-control" value="" disabled>
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
		<span style="font-size:small">Click <a href="#" onclick="manyinvolve()"><i class="fa fa-undo" aria-hidden="true"></i></a></span>
	</div>
	<div style="float:right">
		<button type="button" class="btn btn-success btn-sm" onclick="proceedCharges()">
		Proceed Charges</button>
	</div>
</div>
<div id="results"></div>

<script>

function getItemCodecharges(){
	var mode = 'getItemCodeNewModulesCharges';
	var itemname = $('#item_description').val();
	
	$.post("./Modules/Binalot_Management/actions/actions.php",  { mode: mode, itemname: itemname },
	function(data) {
		$("#results").html(data);
	});
}

function proceedCharges(){
	
	var reportdate = $('#dateselected').val();
	var category = $('#category').val();
	var itemdescription = $('#item_description').val();
	var itemcode = $('#itemcode').val();
	var quantity = $('#quantity').val();
	var unitprice = $('#unitprice').val();
	var remarks = $('#remarks').val();
	    
    if (reportdate =='') {
        	app_alert("System Message", "No Report Date Found", "warning");
        return;
    }
	
	if (itemdescription =='') {
        	app_alert("System Message", "Please select Item", "warning");
        return;
    }
    
	if (category =='') {
        	app_alert("System Message", "No Category Found", "warning");
        return;
    }
	if (itemcode =='') {
        	app_alert("System Message", "No Item Code Found", "warning");
        return;
    }

    if (quantity =='' || quantity <= 0) {
        	app_alert("System Message", "Please select quantity", "warning");
        return;
    }
    
	if (remarks.trim() === '') {
	    app_alert("System Message", "Please enter remarks", "warning");
	    return;
	}
	
	
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: 'getStockInHand', itemcode: itemcode }, function(stockData) {
        var stockinhand = parseInt(stockData);
                
        if(quantity > stockinhand){
            app_alert("System Message", itemdescription+" quantity is greater than the stock in hand value and cannot process this item", "warning");
            return false;
        }

        $('#modaltitle').html("ADD NEW CHARGES MANY INVOLVED DATA");
        $.post("./Modules/Binalot_Management/apps/binalot_add_charges_many_involved_form.php", { 
        	reportdate: reportdate,
        	category: category,
        	itemdescription: itemdescription,
        	itemcode: itemcode,
        	quantity: quantity,
        	unitprice: unitprice,
        	remarks: remarks
        },
        function(data) {		
            $('#formmodal_page').html(data);
            $('#formmodal').show();
        });
    });
	
	
	
}

function manyinvolve(){
	$('#modaltitle').html("ADD NEW CHARGES DATA");
	$.post("./Modules/Binalot_Management/apps/binalot_add_charges_form.php", { },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}


</script>

