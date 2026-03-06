<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
$inventory = new DBCInventory;
$year = date("Y");
$month = date("m");
$min_leadtime = $inventory->GetLeadTime('average_leadtime',$db);
$max_leadtime = $inventory->GetLeadTime('max_leadtime',$db);
$days_count = 31;
if(isset($_POST['location']) && $_POST['location'] != '')
{
	$_SESSION['DBC_ITEM_LOCATION'] = $_POST['location'];
} else {
	unset($_SESSION['DBC_ITEM_LOCATION']);
}
if(isset($_POST['limit']) && $_POST['limit'] != '')
{
	$_SESSION['DBC_SHOW_LIMIT'] = $_POST['limit'];
} else {
	unset($_SESSION['DBC_SHOW_LIMIT']);
}
if($_POST['limit'] != '')
{
	$limit = "LIMIT ".$_POST['limit'];
} else {
	$limit = "";
}
if(isset($_POST['search']))
{
	$search = $_POST['search'];	
	$location = $_POST['location'];
	
	if(isset($_POST['location']) && $_POST['location'] != '')
	{
		$q = "WHERE wms_itemlist.recipient='$location' AND (dbc_inventory_stock.item_code LIKE '%$search%' OR wms_itemlist.item_description LIKE '%$search%')";
	} else {	
		$q = "WHERE dbc_inventory_stock.item_code LIKE '%$search%' OR wms_itemlist.item_description LIKE '%$search%'";
	}

} else {
	if(isset($_POST['location']) && $_POST['location'] != '')
	{
		$search = $_POST['search'];	
		$location = $_POST['location'];
		$q = "WHERE wms_itemlist.recipient='$location'";
	} else {
		$q = "ORDER BY dbc_inventory_stock.supplier_id DESC";
	}
}


?>
<style>
.inventory-shell {
	background:#fff;
	border:1px solid #dfe3e7;
	border-radius:8px;
	box-shadow:0 1px 2px rgba(0,0,0,0.04);
	padding:10px;
}
.inventory-title {
	font-size:14px;
	font-weight:600;
	color:#2f3b4a;
	margin:0 0 8px 2px;
}
.inventory-table {
	margin-bottom:0;
}
.inventory-table thead th {
	background:#16a8a2;
	color:#fff;
	border-color:#11918c;
	font-size:12px;
	font-weight:600;
	white-space:nowrap;
	vertical-align:middle;
}
.inventory-table td {
	padding:5px 8px !important;
	font-size:12px;
	vertical-align:middle;
}
.inventory-table .table-index {
	text-align:center;
	font-weight:600;
	color:#4a5568;
}
.inventory-table .numeric {
	text-align:right;
	padding-right:14px !important;
}
.inventory-table .uom-cell {
	text-align:center !important;
}
.inventory-table .status-cell {
	text-align:center;
}
.status-pill {
	display:inline-flex;
	align-items:center;
	justify-content:center;
	padding:4px 8px;
	border-radius:12px;
	font-size:11px;
	font-weight:600;
	min-width:110px;
	color:#fff;
}
.status-danger {
	background:#dc3545;
}
.status-warning {
	background:#fd7e14;
}
.status-ok {
	background:#198754;
}
.action-cell {
	padding:4px !important;
	text-align:center;
}
.action-cell .btn {
	font-size:11px;
	font-weight:600;
	padding:4px 10px;
}
.sup-title {
	position:absolute;
	display:none;
	border:1px solid #2f80ed;
	padding:5px 10px;
	white-space:nowrap;
	border-radius:5px;
	cursor:pointer;
	left:50px;
	box-shadow:0 2px 8px rgba(0,0,0,0.12);
	z-index:5;
}
</style>
<div class="inventory-shell">
<div class="inventory-title">Inventory Stock Summary</div>
<table style="width: 100%" class="table table-bordered table-striped table-hover inventory-table">
	<thead>
		<tr>
			<th style="width:50px;text-align:center">#</th>
			<th style="width:130px">SUPPLIER ID</th>
			<th style="width:130px">ITEM CODE</th>
			<th>ITEM DESCRIPTION</th>
			<th style="width:80px">STOCK IN HAND</th>
			<th style="width:80px">UOM</th>
			<th style="width:80PX">AVE. DAILY</th>
			<th style="width:80PX">MAX. DAILY</th>
			<th style="width:130px">RE-ORDER LEVEL</th>
			<th style="width:130px">SAFETY STOCKS</th>
			<th style="width: 100PX">INVENTORY STATUS</th>
			<th style="width:90px">ACTION</th>
		</tr>
	</thead>
	<tbody>
<?PHP
	$sqlQuery = "
		SELECT * FROM dbc_inventory_stock 
        INNER JOIN wms_itemlist ON dbc_inventory_stock.item_code = wms_itemlist.item_code
        $q $limit
    ";
	$results = mysqli_query($db, $sqlQuery);    
    if ( $results->num_rows > 0 ) 
    {
    	$i=0;
    	while($INVROW = mysqli_fetch_array($results))  
		{
			$i++;$average=0;
			$rowid = $INVROW['inventory_id'];
			$supplier_id = $INVROW['supplier_id'];
			$item_code = $INVROW['item_code'];
			$item = $INVROW['item_description'];					
			$sqlQueryRecords = "SELECT * FROM dbc_inventory_records WHERE year='$year' AND month='$month' AND item_code='$item_code'";
			$invResults = mysqli_query($db, $sqlQueryRecords);    
		    if ( $invResults->num_rows > 0 ) 
		    {
		    	while($INVENTORYROW = mysqli_fetch_array($invResults))  
				{
			    	$max_average=array();$cnt=0;$max=0;
					for($n = 1; $n <= $days_count; $n++)
					{
					
						$x = str_pad($n, 2, '0', STR_PAD_LEFT);
						if($INVENTORYROW['day_'.$x] > 0)
						{
							$cnt++;
							$average += $INVENTORYROW['day_'.$x];
							$max = $INVENTORYROW['day_'.$x];
							$max_average[] = $max;
							$item_code = $INVENTORYROW['item_code'];							
						}												
					}
					$count = $cnt;
					if($count <= 0)
					{											
						$average_dr = 0;			
						$max_dr = 0;			
						$safety_stocks = 0;			
						$rol = 0;
					} else {
						$average_dr = round($average / $count);			
						$max_dr = max($max_average);			
						$safety_stocks = $max_dr * $max_leadtime - $average_dr * $min_leadtime;	
						$rol = $average_dr * $min_leadtime + $safety_stocks;
					}						
				}				
		    } else {
			    $average_dr = 0;			
				$max_dr = 0;
		    	$rol = 0;
		    	$safety_stocks=0;
		    }
		    if($INVROW['stock_in_hand'] <= $safety_stocks)
			{
				$tdInv_color = 'style="background:#f0284a;color:white;text-align:center"';
				$tdInv_text = "Re Order";
			} 
			elseif($INVROW['stock_in_hand'] < $rol)
			{
				$tdInv_color = 'style="background:orange;color:white;text-align:center"';
				$tdInv_text = "Re Order";
			} else {
				$tdInv_color = 'style="background:green;color:white;text-align:center"';
				$tdInv_text = "Stock Sufficient";
			}

?>			
		<tr>
			<td class="table-index" style="width:50px"><?php echo $i; ?></td>
			<td style="position:relative" id="supplier<?php echo $i; ?>"><?php echo $supplier_id; ?>
				<div class="sup-title" id="supplier_wrapper<?php echo $i; ?>" style="background:#ffffff !important">
					<?php echo $function->GetSupplierName($supplier_id,$db); ?>
				</div>
			</td>
			<td><?php echo $item_code; ?></td>
			<td><?php echo $INVROW['item_description']; ?></td>
			<td class="numeric"><?php echo $INVROW['stock_in_hand']; ?></td>
			<td class="uom-cell"><?php echo $INVROW['uom']; ?></td>
			<td class="numeric"><?php echo $average_dr; ?></td>
			<td class="numeric"><?php echo $max_dr; ?></td>
			<td class="numeric"><?php echo $rol; ?></td>
			<td class="numeric"><?php echo $safety_stocks; ?></td>
			<td class="status-cell">
				<span class="status-pill <?php echo $INVROW['stock_in_hand'] <= $safety_stocks ? 'status-danger' : ($INVROW['stock_in_hand'] < $rol ? 'status-warning' : 'status-ok'); ?>"><?php echo $tdInv_text; ?></span>
			</td>
			<td class="action-cell">
				<div class="btn-group" role="group" aria-label="Ronan Sarbon">
					<button class="btn btn-primary btn-sm" onclick="itemDetails('<?php echo $item_code; ?>','<?php echo $item; ?>')">Item Details</button>
				</div>
			</td>
		</tr>
<script>
$(function()
{
	$('#supplier' + '<?php echo $i; ?>').mouseenter(function()
	{
    	$('#supplier_wrapper' + '<?php echo $i; ?>').show();
		}).mouseleave(function()
	{
    	$('#supplier_wrapper' + '<?php echo $i; ?>').hide();
	});
});
</script>		
<?PHP 	} } else { ?>
		<tr>
			<td colspan="12" style="text-align:center"><i class="fa fa-bell"></i>&nbsp;&nbsp;No Records</td>
		</tr>
<?PHP } ?>			
	</tbody>
</table>
</div>
<script>
function itemDetails(itemcode,item)
{
	
}
function itemDetails(itemcode,item)
{
	$('#modaltitle').html("ITEM DETAILS");
	$.post("./Modules/DBC_Management/apps/inventory_item_details.php", { item_code: itemcode, item: item },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
</script>
