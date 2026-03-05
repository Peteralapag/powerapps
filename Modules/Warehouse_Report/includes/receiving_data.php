<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$userlevel = $_SESSION['application_userlevel'];
if(isset($_POST['search']))
{
	$search = $_POST['search'];
	$q = "WHERE item_code LIKE '%$search%' OR class LIKE '%$search%' OR item_description LIKE '%$search%'  OR item_description LIKE '%$search%'";
} 
else
{
	$q = '';
}
?>
<style>
.my-wrapper th {white-space:nowrap !important;}
.my-wrapper .editable td {padding:0 !important;background:#fff !important;}
.my-wrapper .editable td input[type=text] {border:0 !important;}
.my-wrapper .editable {display: none;}
.datarow {
	background:#e5e8e9;
}
.editable input[type=text] {
	background:#fff7d1;
	font-size:15px;
	color:#636363;
}
.editable td {
	background:#fff7d1 !important;
}
</style>
<table style="width:100%" class="table table-bordered my-wrapper">
	<thead>
		<tr>
			<th>#</th>
			<th>ITEM CODE&nbsp;</th>
			<th>CLASS</th>
			<th style="width:300px !important">ITEM DESCRIPTION</th>			
			<th>RECEIVED AMT.</th>
			<th>UOM</th>
			<th>COVERSION</th>
			<th>P.O No.</th>
			<th>SUPPLIER</th>
			<th>INVOICE No.</th>
			<th>MRCS No.</th>
			<th>UNIT PRICE</th>
			<th>EXPIRATION DATE</th>
			<th>DELIVERY DATE</th>
			<th>RECEIVED BY</th>
			<th>RECEIVED DATE</th>
			<th style="width:110px;">ACTIONS</th>
		</tr>
	</thead>
	<tbody>
<?php
	$query = "SELECT * FROM rpt_item_receiving $q";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{	
		$j=0;
		while($RECEIVEROW = mysqli_fetch_array($results))  
		{
			$j++;
			$rowid = $RECEIVEROW['id'];

?>	
		<tr  class="datarow" id="datarow<?php echo $j; ?>" ondblclick="editRow('<?php echo $j; ?>','open')">
			<td><?PHP echo $j; ?></td>
			<td><?PHP echo $RECEIVEROW['item_code']; ?></td>
			<td><?PHP echo $RECEIVEROW['class']; ?></td>
			<td><?PHP echo $RECEIVEROW['item_description']; ?></td>			
			<td><?PHP echo $RECEIVEROW['received_amount']; ?></td>
			<td><?PHP echo $RECEIVEROW['uom']; ?></td>
			<td><?PHP echo $RECEIVEROW['conversion']; ?></td>
			<td><?PHP echo $RECEIVEROW['po_number']; ?></td>
			<td><?PHP echo $RECEIVEROW['supplier']; ?></td>
			<td><?PHP echo $RECEIVEROW['invoice_no']; ?></td>
			<td><?PHP echo $RECEIVEROW['mrcs_no']; ?></td>
			<td><?PHP echo $RECEIVEROW['unit_price']; ?></td>
			<td><?PHP echo $RECEIVEROW['expiration_date']; ?></td>
			<td><?PHP echo $RECEIVEROW['delivery_date']; ?></td>
			<td><?PHP echo $RECEIVEROW['received_by']; ?></td>
			<td><?PHP echo $RECEIVEROW['date_received']; ?></td>
			<td style="padding:2px !important;text-align:center">
				<button class="btn btn-success btn-sm w-100" onclick="editRow('<?php echo $j; ?>','open')"><i class="fa-solid fa-pen-to-square"></i> Edit</button>				
			</td>
		</tr>
		<tr class="editable" id="datarowedit<?php echo $j; ?>">
			<td style="text-align:center;">*</td>
			<td><input id="item_code<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['item_code']; ?>" onchange="saveThis('item_code','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="class<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['class']; ?>" onchange="saveThis('class','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="item_description<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['item_description']; ?>" onchange="saveThis('item_description','<?php echo $j; ?>','<?php echo $rowid; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="received_amount<?php echo $j; ?>" type="text" class="form-control" style="text-align:right" value="<?PHP echo $RECEIVEROW['received_amount']; ?>" onchange="saveThis('received_amount','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="uom<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['uom']; ?>" onchange="saveThis('uom','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="conversion<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['conversion']; ?>" onchange="saveThis('conversion','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="po_number<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['po_number']; ?>" onchange="saveThis('po_number','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="supplier<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['supplier']; ?>" onchange="saveThis('supplier','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="invoice_no<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['invoice_no']; ?>" onchange="saveThis('invoice_no','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="mrcs_no<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['mrcs_no']; ?>" onchange="saveThis('mrcs_no','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="unit_price<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['unit_price']; ?>" onchange="saveThis('unit_price','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="expiration_date<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['expiration_date']; ?>" onchange="saveThis('expiration_date','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>			
			<td><input id="delivery_date<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['delivery_date']; ?>" onchange="saveThis('delivery_date','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td><input id="received_by<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['received_by']; ?>" onchange="saveThis('received_by','<?php echo $j; ?>','<?php echo $rowid; ?>')" disabled></td>
			<td><input id="date_received<?php echo $j; ?>" type="text" class="form-control" value="<?PHP echo $RECEIVEROW['date_received']; ?>" onchange="saveThis('date_received','<?php echo $j; ?>','<?php echo $rowid; ?>')"></td>
			<td style="padding:2px !important;text-align:center">
				<button class="btn btn-warning btn-sm w-50" onclick="editRow('<?php echo $j; ?>','close')"><i class="fa-solid fa-x"></i></button>
				<button class="btn btn-danger btn-sm w-50" onclick="deleteReceiving('<?php echo $rowid; ?>')"><i class="fa-solid fa-trash color-white"></i></button>
			</td>
		</tr>
<?php }} else { ?>
		<tr>
			<td colspan="17" style="text-align:center"><i class="fa fa-bell"></i> No Records.</td>
		</tr>
<?php } ?>		
	</tbody>		
</table>
<div class="resultas"></div>
<script>
function deleteReceiving(params)
{
	app_confirm("Delete Receiving","Are you sure to delete this Receiving Data??","warning","deletereceiving",params,"red");
	return false;
}
function deleteReceivingYes(params)
{
	var mode = 'deletereceiving';
	rms_reloaderOn("Deleting! Please Wait...");
	setTimeout(function()
	{		
		$.post('../../../Modules/' + sessionStorage.module + '/actions/actions.php', { mode: mode, rowid: params },
		function(data) {
			$('.results').html(data);		 		
	 		rms_reloaderOff();
		});
	},1000);
}

function saveThis(column,elemid,rowid)
{
	var mode = 'updatethisreceiving';
	var value = $('#' + column + elemid).val();
	rms_reloaderOn();
	setTimeout(function()
	{
		$.post('modules/' + sessionStorage.module + '/actions/actions.php', { mode: mode, rowid: rowid, column, column, value: value },
		function(data) {
			$('.resultas').html(data);
			rms_reloaderOff();
		});
	},200);
}
function editRow(elemid,cmd)
{	
	if($('#' + sessionStorage.datarowedit).is(':visible'))
	{
		$('#' + sessionStorage.datarowedit).hide();
		$('#' + sessionStorage.datarow).show();
	}
	if(cmd == 'open')
	{		
		$('#datarow' + elemid).hide();
		$('#datarowedit' + elemid).show();
		sessionStorage.setItem("datarowedit", "datarowedit" + elemid);
		sessionStorage.setItem("datarow", "datarow" + elemid);
	}
	else if(cmd == 'close')
	{
		$('#datarow' + sessionStorage.showhide).hide();
		$('#datarowedit' + elemid).hide();
		$('#datarow' + elemid).show();
		sessionStorage.removeItem("datarowedit");
		sessionStorage.removeItem("datarow");
	}
}
</script>
