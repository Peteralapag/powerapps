<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php") ;
$mode = $_POST['params'];
$function = new BINALOTFunctions;
if($_POST['params'] == 'edit')
{
	$rowid = $_POST['rowid'];
	$QUERY = "SELECT * FROM binalot_itemlist WHERE id='$rowid'";
	$result = mysqli_query($db, $QUERY );    
    if ( $result->num_rows > 0 ) 
    {
		while($ROW = mysqli_fetch_array($result))  
		{
			$rowid = $ROW['id'];
			$supplier_id = $ROW['supplier_id'];
			$item_code = $ROW['item_code'];
			$qr_code = $ROW['qr_code'];
			$category = htmlspecialchars($ROW['category'] ?? '', ENT_QUOTES, 'UTF-8');
			$recipient = $ROW['recipient'];
			$item_location = $ROW['item_location'];
			$class = $ROW['class'];
			$item_description = $ROW['item_description'];
			$unit_price = $ROW['unit_price'];
			$uom = $ROW['uom'];
//			$conversion = $ROW['conversion'];
			$yieldperbatch = $ROW['yield_perbatch'];
			$added_by = $ROW['added_by'];
			$date_added = $ROW['date_added'];
			$active = $ROW['active'];
			
			if($ROW['active'] == 1)
			{
				$checked = 'checked="checked"';
			} 
			if($ROW['active'] == 0)
			{
				$checked = '';
			}
			if($ROW['date_added'] != '')
			{
				$date_added = date("F m, Y @h:i A");
			} else {
				$date_added = "--|--";
			}
		}
    }
}
if($_POST['params'] == 'add')
{
	$rowid = "";
	$supplier_id = "";
	$item_code = "";
	$qr_code =  "";
	$category =  "";
	$recipient =  "";
	$item_location = "";
	$class =  "";
	$item_description =  "";
	$uom =  "";
	$yieldperbatch =  "";
	$added_by =  "";
	$date_added =  "";
	$active =  "";
	$checked = "";
	$unit_price = "0.00";
}
?>
<style>
.form-wrapper {width:500px;max-height:500px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr>
			<th>Supplier</th>
			<td>
				<input type="hidden" value="<?php echo $rowid; ?>">
				<input id="supplier" type="text" class="form-control" value="<?php echo $supplier_id; ?>" disabled>
			</td>		
			
		</tr>
		<tr>
			<th>Recipient</th>
			<td>
				<input id="recipient" type="text" class="form-control" value="BINALOT" disabled>
			</td>
		</tr>
		<tr>
			<th>Item Loc.</th>
			<td>
				<input id="item_location" type="text" class="form-control" value="BINALOT" disabled>
			</td>
		</tr>
		<tr>
			<th>Item Code</th>
			<td>
				<input id="item_code" type="text" class="form-control" value="<?php echo $item_code; ?>">				
			</td>
		</tr>
		<tr>
			<th>QR Code</th>
			<td>
				<input id="qr_code" type="text" class="form-control" value="<?php echo $qr_code; ?>" placeholder="Leave for now" disabled>
			</td>
		</tr>
		<tr>
			<th>Category</th>
			<td>
				<select id="categories" class="form-control">
					<?php echo $function->GetItemCategory($category,$db)?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Item Description</th>
			<td><input id="item_description" type="text" class="form-control" value="<?php echo $item_description; ?>"></td>
		</tr>
		<tr>
			<th>Units of Measure</th>
			<td>
				<select id="uom" class="form-control">
					<?php echo $function->GetUOM($uom,$db)?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Yield per Batch</th>
			<td><input id="yieldperbatch" type="number" class="form-control" value="<?php echo $yieldperbatch; ?>"></td>
		</tr>

		<tr>
			<th>Unit Price</th>
			<td><input id="unit_price" type="number" class="form-control" value="<?php echo $unit_price; ?>" placeholder="<?php echo $unit_price; ?>"></td>
		</tr>
	<?php if($mode == 'edit') { ?>
		<tr>
			<th>Added By</th>
			<td><input id="added_by" type="text" class="form-control" value="<?php echo $added_by; ?>" disabled></td>
		</tr>
		<tr>
			<th>Date Added</th>
			<td><input id="date_added" type="text" class="form-control" value="<?php echo $date_added; ?>" disabled></td>
		</tr>
	<?php } ?>
		<tr>
			<th>Active</th>
			<td>
				<label class="switch">
					<input id="active" type="checkbox" <?php echo $checked; ?>>
					<span class="slider round"></span>
				</label>
			</td>
		</tr>
	</table>
</div>
<div class="results" style="font-size:12px;"></div>
<div style="margin-top:10px;text-align:right">
	<?php if($mode == 'add') { ?>
	<button class="btn btn-primary btn-sm" onclick="validateForm()">Save 
	Itemlist</button>
	<?php } if($mode == 'edit') { ?>
	<button class="btn btn-primary btn-sm" onclick="validateForm()">Update Itemlist</button>
	<button class="btn btn-warning btn-sm" onclick="deleteItem('<?php echo $rowid; ?>')">Delete Item</button>
	<?php } ?>
	<button class="btn btn-danger btn-sm" onclick="closeModal('formmodal')">Close</button>
</div>
<script>
function deleteItem(rowid)
{
	app_confirm("Delete","Are you sure to delete this Item?","warning","deleteitem",rowid,"red");
	return false;
}
function deleteItemYes(params)
{
	rms_reloaderOn("Deleting...");
	setTimeout(function()
	{
		var limit = $('#limit').val();
		$.post("./Modules/Binalot_Management/actions/deleteitem_process.php", { rowid: params,limit: limit },
		function(data) {		
			$('.results').html(data);
			rms_reloaderOff();
		});
	},1000);
}
function validateForm()
{
	var mode = '<?php echo $mode; ?>';
	var rowid = '<?php echo $rowid; ?>';
	var limit = $('#limit').val(); 
	var supplier = $('#supplier').val(); 
	var recipient = $('#recipient').val(); 
	var item_location = $('#item_location').val();
	var item_code = $('#item_code').val();
	var qr_code = $('#qr_code').val();
	var category = $('#categories').val();
	var item_description = $('#item_description').val();
	var uom = $('#uom').val();
	var yieldperbatch = $('#yieldperbatch').val();
	var unit_price = $('#unit_price').val();
	var active = $('#active').val();
	if(recipient === '')
	{
		app_alert("Recipient","Please select Recipient","warning","Ok","recipient","focus");
		return false;
	}
	if(item_location === '')
	{
		app_alert("Item Location","Please select Item Location","warning","Ok","item_location","focus");
		return false;
	}

	if(item_code === '')
	{
		app_alert("Item Code","Please enter Item Code","warning","Ok","item_code","focus");
		return false;
	}
	if(category == '')
	{
		app_alert("Category","Please select Categorya","warning","Ok","category","focus");
		return false;
	}
	if(item_description === '')
	{
		app_alert("item Description","Please enter the Description","warning","Ok","item_description","focus");
		return false;
	}
	if(uom === '')
	{
		app_alert("Units of Measures","Please select units of Measurements","warning","Ok","uom","focus");
		return false;
	}	
	if(unit_price < 0 || unit_price === '')
	{
		app_alert("Unit Price","Please enter Unit Price","warning","Ok","unit_price","focus");
		return false;
	}	
	if($('#active').is(":checked") == true)
	{
		var active = 1;
	} else {
		var active = 0;
	}
	if(mode == 'add')
	{
		rms_reloaderOn("Saving to Itemlist...");
	} 
	if(mode == 'edit')
	{
		rms_reloaderOn("Updating Itemlist...");
	} 
	setTimeout(function()
	{
		$.post("./Modules/Binalot_Management/actions/itemlist_process.php",
		{ 
			mode: mode,
			rowid: rowid,
			active: active, 
			recipient: recipient, 
			item_location: item_location,
			item_code: item_code,
			qr_code: qr_code,
			category: category, 
			item_description: item_description,
			uom: uom,
			yieldperbatch : yieldperbatch,
			unit_price: unit_price,
			active : active, 
			limit: limit,
			supplier: supplier
		},
		function(data) {		
			$('.results').html(data);
			rms_reloaderOff();
		});
	},1000);
}
</script>

