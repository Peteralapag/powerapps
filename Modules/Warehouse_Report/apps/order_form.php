<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT'].'/Modules/Warehouse_report/class/wh_functions.class.php';
$wh_functions = new WHFunctions;
$prepared_by = $_SESSION['application_appnameuser'];
$date = date("Y-m-d");
if($_POST['mode'] == 'edit')
{
	$rowid = $_POST['rowid'];
	$editQuery = "SELECT * FROM rpt_orders WHERE id='$rowid'";
	$editResults = mysqli_query($db, $editQuery);    
	while($ROWS = mysqli_fetch_array($editResults))  
	{
		$rowid = $ROWS['id'];
		$item_code = $ROWS['item_code'];
		$class = $ROWS['class'];
		$item_description = $ROWS['item_description'];
		$quantity = $ROWS['on_hand'];
		$uom = $ROWS['uom'];
		$conversion = $ROWS['conversion'];
		$supplier = $ROWS['supplier'];

		$unit_price = $ROWS['unit_price'];
		$expiration_date = date("Y-m-d", strtotime($ROWS['expiration_date']));
		$added_by = $ROWS['added_by'];
		$date_added = $ROWS['date_added'];
	}
}
else if($_POST['mode'] == 'new')
{
	$rowid = '';
	$item_code = '';
	$class = '';
	$item_description = '';
	$quantity = '';
	$uom = '';
	$conversion = '';
	$mrs_no = '';
	$delivered_to = '';
	$expiration_date = '';
	$delivery_date = '';
//	$prepared_by = '';
}
else {
	echo "wrong";
}
?>
<div style="width:500px;margin-bottom:10px; max-height:450px; overflow:auto;">
	<table style="width: 100%" class="table tdtd">
		<tr>
			<th>DATE ISSUED</th>
			<td>&nbsp;</td>
			<td><input id="date_issued" type="date" class="form-control form-control-sm" value="<?php echo $date; ?>"></td>
		</tr>
		<tr>
			<th>CLASSIFICATION</th>
			<td>&nbsp;</td>
			<td><input id="itemclass" type="text" class="form-control form-control-sm" value="<?php echo $class; ?>" disabled></td>
		</tr>
		<tr>
			<th>ITEM CODE</th>
			<td>&nbsp;</td>
			<td><input id="itemcode" type="text" class="form-control form-control-sm" value="<?php echo $item_code; ?>" disabled></td>
		</tr>
		<tr>
			<th>ITEM DESCRIPTION</th>
			<td>&nbsp;</td>
			<td>
				<input list="itemlist" id="itemname" class="form-control form-control-sm" placeholder="Select Item" autocomplete="no" onchange="getDefaultValue(this.value)">
				<datalist id="itemlist">
					<?php echo $wh_functions->GetItemsDD($db); ?>
				</datalist>
			</td>
		</tr>
		<tr>
			<th>QUANTITY</th>
			<td>&nbsp;</td>
			<td><input id="quantity" type="text" class="form-control form-control-sm" value="<?php echo $quantity; ?>"></td>
		</tr>
		<tr>
			<th>UOM</th>
			<td>&nbsp;</td>
			<td>
				<select id="uom" class="form-control form-control-sm">
					<?php echo $wh_functions->GetUOM('<?php echo $uom; ?>',$db); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>CONVERSION</th>
			<td>&nbsp;</td>
			<td><input id="conversion" type="text" class="form-control form-control-sm" value="<?php echo $conversion; ?>" disabled></td>
		</tr>
		<tr>
			<th>MRS No.</th>
			<td>&nbsp;</td>
			<td><input id="mrsno" type="text" class="form-control form-control-sm" value="<?php echo $class; ?>"></td>
		</tr>
		<tr>
			<th>DELIVERED TO</th>
			<td>&nbsp;</td>
			<td><input id="delivered_to" type="text" class="form-control form-control-sm" value="<?php echo $delivered_to; ?>"></td>
		</tr>
		<tr>
			<th>DELIVERY DATE</th>
			<td>&nbsp;</td>
			<td><input id="delivery_date" type="date" class="form-control form-control-sm" value="<?php echo $delivery_date; ?>"></td>
		</tr>
		<tr>
			<th>EXPIRATION DATE</th>
			<td>&nbsp;</td>
			<td><input type="date" class="form-control form-control-sm" value="<?php echo $expiration_date; ?>"></td>
		</tr>
		<tr>
			<th>PREPARED BY</th>
			<td></td>
			<td><input id="prepairedby" type="text" class="form-control form-control-sm" value="<?php echo $prepared_by; ?>" disabled></td>
		</tr>		
	</table>
</div>
<div style="margin-top:20px;text-align:right">
<?php if($_POST['mode'] == 'edit') { ?>
	<button class="btn btn-success btncontrol" onclick="saveSupplier('update')">Update</button>
	<button class="btn btn-warning btncontrol color-white" onclick="deleteSupplier('<?php /* echo $rowid; */ ?>')">Delete</button>
<?php } else { ?>
	<button class="btn btn-success btncontrol" onclick="saveOrder('save')">Save</button>
<?php } ?>
	<button class="btn btn-danger btncontrol" onclick="closeModal('formmodal')">Cancel</button>
</div>
<div class="results"></div>
<script>
function getDefaultValue(setval)
{
	var mode = 'setval';
	$.post("modules/" + sessionStorage.module + "/actions/actions.php", { mode: mode, itemname: setval },
	function(data) {
		$('.results').html(data);
	});
}
function saveOrder(params)
{
	var rowid = '<?php echo $rowid; ?>';
	var date_issued = $('#date_issued').val();
	var itemcode = $('#itemcode').val();
	var itemname = $('#itemname').val();
	var quantity = $('#quantity').val();
	var itemclass = $('#itemclass').val();
	var uom = $('#uom').val();
	var conversion = $('#conversion').val();
	var mrsno = $('#mrsno').val();
	var delivered_to = $('#delivered_to').val();
	var delivery_date = $('#delivery_date').val();
	var expiration_date = $('#expiration_date').val();
	var prepared_by = $('#prepared_by').val();

	if(date_issued === '')
	{
		app_alert("Date Issued","Please select date of issuance","warning","Ok","date_issued","focus");
		return false;
	}
	if(itemname === '')
	{
		app_alert("Item Name","Please enter the ITEM NAME","warning","Ok","itemname","focus");
		return false;
	}
	if(quantity <= 0)
	{
		app_alert("Quantity","Please enter the number of quantity received","warning","Ok","quantity","focus");
		return false;
	}
	if(uom === '')
	{
		app_alert("Units of Measurement","Please Select Units of Measures","warning","Ok","uom","focus");
		return false;
	}
	if(conversion === '')
	{
		app_alert("Units of Measurement","Please Enter Conversion of Units","warning","Ok","conversion","focus");
		return false;
	}
	if(mrsno === '')
	{
		app_alert("MRS Number","Please Enter MRS Number","warning","Ok","mrsno","focus");
		return false;
	}
	if(delivered_to === '')
	{
		app_alert("Delivered To?","Please where/whom to deliver this order","warning","Ok","delivered_to","focus");
		return false;
	}
	if(delivery_date === '')
	{
		app_alert("Delivery Date","Please select date of delivery","warning","Ok","delivery_date","focus");
		return false;
	}
	if(expiration_date === '')
	{
		app_alert("Expiration Date","Please select date of Expiration","warning","Ok","expiration_date","focus");
		return false;
	}
	var mode = 'saveorder';
	rms_reloaderOn("Saving! Please Wait...");
	$('.btncontrol').prop('disabled', true);
	setTimeout(function()
	{		
		$.post('../../../Modules/' + sessionStorage.module + '/actions/actions.php', { 
			params: params,
			rowid: rowid,
			date_issued: date_issued,
			itemcode: itemcode,
			itemname: itemname,
			quantity: quantity,
			itemclass: itemclass,
			uom: uom,
			conversion: conversion,
			mrsno: mrsno,
			delivered_to: delivered_to,
			delivery_date: delivery_date,
			expiration_date: expiration_date,
			prepared_by: prepared_by
		},
		function(data) {
			$('.results').html(data);		 		
	 		$('.btncontrol').prop('disabled', false);
	 		rms_reloaderOff();
		});
	},1000);
}
</script>
