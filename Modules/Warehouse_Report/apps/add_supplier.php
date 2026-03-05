<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT'].'/Modules/Warehouse_report/class/wh_functions.class.php';
$wh_functions = new WHFunctions;
if($_POST['mode'] == 'edit')
{
	$rowid = $_POST['rowid'];
	$editQuery = "SELECT * FROM rpt_item_records WHERE id='$rowid'";
	$editResults = mysqli_query($db, $editQuery);    
	while($ROWS = mysqli_fetch_array($editResults))  
	{
		$rowid = $ROWS['id'];
		$item_code = $ROWS['item_code'];
		$class = $ROWS['class'];
		$item_description = $ROWS['item_description'];
		$on_hand = $ROWS['on_hand'];
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
	$item_code = $_POST['itemcode'];
	$class = $_POST['classes'];
	$item_description = strtoupper($_POST['itemname']);
	$on_hand = '0';
	$uom = $_POST['uom'];
	$conversion = '';
	$supplier =
	$unit_price = '';
	$expiration_date = '';
	$added_by = '';
	$date_added = '';
}
else {
	echo "wrong";
}
?>
<div style="width:500px;margin-bottom:10px; max-height:450px; overflow:auto;">
	<table style="width: 100%" class="table tdtd">
		<tr>
			<th>ITEM CODE</th>
			<td>&nbsp;</td>
			<td><input id="itemcode" type="text" class="form-control form-control-sm" value="<?php echo $item_code; ?>" disabled></td>
		</tr>
		<tr>
			<th>ITEM DESCRIPTION</th>
			<td>&nbsp;</td>
			<td><input id="itemname" type="text" class="form-control form-control-sm" value="<?php echo $item_description; ?>" disabled></td>
		</tr>
		<tr>
			<th>CLASSIFICATIONS</th>
			<td>&nbsp;</td>
			<td>
				<select id="itemclass" class="form-control form-control-sm">
					<?php echo $wh_functions->ItemClass($class,$db); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>UNITS OF MEASURE</th>
			<td>&nbsp;</td>
			<td>
				<select id="uom" class="form-control form-control-sm">
					<?php echo $wh_functions->GetUOM($uom,$db); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>CONVERSION</th>
			<td>&nbsp;</td>
			<td><input id="conversion" type="text" class="form-control form-control-sm" value="<?php echo $conversion; ?>"></td>
		</tr>
		<tr>
			<th>UNIT PRICE</th>
			<td>&nbsp;</td>
			<td><input id="unitprice" type="number" class="form-control form-control-sm" value="<?php echo $unit_price; ?>"></td>
		</tr>
		<tr>
			<th>SUPPLIER NAME</th>
			<td>&nbsp;</td>
			<td><input id="supplier" type="text" class="form-control form-control-sm" value="<?php echo $supplier; ?>"></td>
		</tr>
	<?php if($_POST['mode'] == 'edit') { ?>
		<tr>
			<th>ADDED BY</th>
			<td>&nbsp;</td>
			<td><input type="text" class="form-control form-control-sm" value="<?php echo $added_by;?>" disabled></td>
		</tr>
		<tr>
			<th>DATE ADDED</th>
			<td>&nbsp;</td>
			<td><input type="text" class="form-control form-control-sm" value="<?php echo date("M d, Y @ h:i A", strtotime($date_added)); ?>" disabled></td>
		</tr>
	<?php } ?>
	</table>
</div>
<div style="margin-top:20px;text-align:right">
<?php if($_POST['mode'] == 'edit') { ?>
	<button class="btn btn-success btncontrol" onclick="saveSupplier('update')">Update</button>
	<button class="btn btn-warning btncontrol color-white" onclick="deleteSupplier('<?php echo $rowid; ?>')">Delete</button>
<?php } else { ?>
	<button class="btn btn-success btncontrol" onclick="saveSupplier('save')">Save</button>
<?php } ?>
	<button class="btn btn-danger btncontrol" onclick="closeModal('formodalsm')">Cancel</button>
</div>
<div class="results"></div>
<script>
function insertNewStocks(rowid)
{
	var mode = 'insertnewstocks';
	$('#modaltitle').html('INSERT NEW STOCKS');
	$.post("modules/" + sessionStorage.module + "/apps/add_item.php", { mode: mode },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function deleteSupplier(params)
{
	app_confirm("Delete Supplier","Are you sure to delete this Supplier?","warning","deletesupplier",params,"red");
	return false;
}
function deleteSupplierYes(params)
{
	var mode = 'deletesupplier';
	rms_reloaderOn("Deleting! Please Wait...");
	$('.btncontrol').prop('disabled', true);
	setTimeout(function()
	{		
		$.post('../../../Modules/' + sessionStorage.module + '/actions/actions.php', { mode: mode, rowid: params },
		function(data) {
			$('.results').html(data);		 		
	 		$('.btncontrol').prop('disabled', false);
	 		closeModal('formodalsm');
	 		rms_reloaderOff();
		});
	},1000);
}
function saveSupplier(params)
{
	var rowid = '<?php echo $rowid; ?>';
	var itemcode = $('#itemcode').val();
	var itemname = $('#itemname').val();
	var itemclass = $('#itemclass').val();
	var uom = $('#uom').val();
	var conversion = $('#conversion').val();
	var unitprice = $('#unitprice').val();
	var supplier = $('#supplier').val();
	if(itemcode === '')
	{
		app_alert("Item Code","Please enter the ITEM CODE","warning","Ok","itemcode","focus");
		return false;
	}
	if(itemname === '')
	{
		app_alert("Item Name","Please enter the ITEM NAME","warning","Ok","itemname","focus");
		return false;
	}
	if(itemclass === '')
	{
		app_alert("Item Class","Please enter the ITEM CLASS","warning","Ok","itemclass","focus");
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
	if(unitprice === '' || unitprice < 0)
	{
		app_alert("Unit Price","Please Enter Unit Price","warning","Ok","unitprice","focus");
		return false;
	}
	if(supplier === '')
	{
		app_alert("Supplier","Please Enter Supplier`s Name","warning","Ok","supplier","focus");
		return false;
	}
	var mode = 'savesupplier';
	rms_reloaderOn("Saving! Please Wait...");
	$('.btncontrol').prop('disabled', true);
	setTimeout(function()
	{		
		$.post('../../../Modules/' + sessionStorage.module + '/actions/actions.php', { 
			mode: mode,
			params: params,
			rowid: rowid,
			itemcode: itemcode,
			itemname: itemname,
			itemclass: itemclass,
			uom: uom,
			conversion: conversion,
			unitprice: unitprice,
			supplier: supplier
		},
		function(data) {
			$('.results').html(data);		 		
	 		$('.btncontrol').prop('disabled', false);
	 		rms_reloaderOff();
		});
	},1000);

}
</script>
