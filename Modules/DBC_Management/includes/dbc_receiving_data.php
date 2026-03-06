<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$date = date("Y-m-d");

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    // Validate and sanitize user input for limit
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        // Set session and query limit
        $_SESSION['DBC_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        // Handle invalid input
        $limitClause = "";
        $_SESSION['DBC_SHOW_LIMIT'] = $limitClause;
        // You might want to set an error message here
    }
} else {
    $limitClause = "";
    $_SESSION['DBC_SHOW_LIMIT'] = $limitClause;
}

if(isset($_POST['transdate'])) {
	$transdate = $_POST['transdate'];
	$_SESSION['DBC_TRANSDATE'] = $transdate;
} else {
	$transdate = $_SESSION['DBC_TRANSDATE'];
}

?>
<style>
.receiving-section {
	background:#ffffff;
	border:1px solid #dfe3e7;
	border-radius:8px;
	padding:10px;
	box-shadow:0 1px 2px rgba(0,0,0,0.04);
	margin-bottom:12px;
}
.section-title {
	font-size:14px;
	font-weight:600;
	color:#2f3b4a;
	margin:0 0 8px 2px;
	letter-spacing:0.2px;
}
.professional-table {
	margin-bottom:0;
}
.professional-table thead th {
	background:#16a8a2;
	color:#fff;
	font-size:12px;
	font-weight:600;
	white-space:nowrap;
	border-color:#11918c;
	vertical-align:middle;
}
.professional-table td {
	padding:5px 8px !important;
	font-size:12px;
	vertical-align:middle;
}
.professional-table .table-index {
	text-align:center;
	font-weight:600;
	color:#4a5568;
}
.professional-table .qty-cell,
.professional-table .remarks-cell {
	text-align:center;
	border-radius:4px;
}
.professional-table .editable-cell {
	background:#fff8e8;
	outline:none;
}
.professional-table .cell-editor {
	min-height:28px;
	display:flex;
	align-items:center;
	justify-content:center;
	text-align:center;
	line-height:1.2;
	padding:2px 4px;
	width:100%;
	border-radius:4px;
}
.status-pill {
	display:inline-flex;
	align-items:center;
	justify-content:center;
	gap:5px;
	padding:4px 8px;
	border-radius:14px;
	font-size:11px;
	font-weight:600;
	color:#fff;
	min-width:108px;
}
.status-received {
	background:#198754;
}
.status-void {
	background:#dc3545;
}
.action-stack {
	display:flex;
	flex-direction:column;
	gap:4px;
}
.action-stack .btn {
	font-size:11px;
	font-weight:600;
	padding:3px 8px;
	width:110px;
}
.summary-head th {
	background:#0f7f7a !important;
	border-color:#0f7f7a !important;
}
.totals-row {
	background:#eef1f4;
	font-weight:700;
}
</style>
<div class="receiving-section">
	<div class="section-title">Production Receiving Details</div>
	<table style="width: 100%" class="table table-bordered table-striped table-hover professional-table">
	<thead>
		<tr>
			<th style="width:50px;text-align:center">#</th>
			<th>REPORT DATE</th>
			<th>TIME</th>
			<th>SUPPLIER</th>
			<th>CATEGORY</th>
			<th>ITEM DESCRIPTION</th>
			<th>ITEM CODE</th>
			<th>CTRL NO.</th>
			<th>CREATED BY</th>
			<th>ACTUAL PRODUCED</th>
			<th>REMARKS</th>
			<th>ACTIONS</th>
			
		</tr>
	</thead>
	<tbody>	
<?PHP
	$sqlQuery = "SELECT * FROM dbc_dbc_production WHERE report_date='$transdate' ORDER BY receiving_detail_id DESC";
	$results = mysqli_query($db, $sqlQuery);    
    if ( $results->num_rows > 0 ) 
    {
    	$n=0;
    	while($RECVROW = mysqli_fetch_array($results))  
		{
			$n++;
			$id = $RECVROW['receiving_detail_id'];
			
			$reportdate = $RECVROW['report_date'];
			$createdtime = $RECVROW['created_time'];
			$shift = $RECVROW['shift'];
			$category = $RECVROW['category'];
			$itemdescription = $RECVROW['item_description'];
			$itemcode = $RECVROW['item_code'];
			$qty = $RECVROW['quantity_received'];
			$postedby = $RECVROW['posted_by'];
			$confirmedby = $RECVROW['confirmed_by'];
			$status = $RECVROW['status'];
			
			$stat = $status == 'Yes'? 'bg-success': 'bg-danger';
			$statData = $status == 'Yes'? 'Received Item': 'Void Item';
			$statfa = $status == 'Yes'? 'fa-check': 'fa-exclamation-circle';
			$fastyle = $status == 'Yes'? '': '';
			$spanstyle = $status == 'Yes'? 'background:#198754': 'background:#dc3545';
			
			$pcount = $status == 'No'? '' : $function->GetDataByRecevingId('pcount','dbc_fgts_pcount',$id,$db);
			$remarks = $status == 'No'? '' : $function->GetDataByRecevingId('remarks','dbc_fgts_pcount',$id,$db);
			
			$pcounteditable = $status == 'No'? 'contenteditable="true"': '';
			
?>	
		<tr>
			<td class="table-index"><?php echo $n; ?></td>
			<td><?php echo $reportdate?></td>
			<td><?php echo $createdtime?></td>
			<td><span class="badge badge-light" style="font-size:11px;border:1px solid #d7dde3;color:#495057;">FD RECEPTION AREA</span></td>
			<td><?php echo $category?></td>
			<td title="<?php echo $itemdescription?>"><?php echo $function->limitStringLength($itemdescription, 30)?></td>
			<td><?php echo $itemcode?></td>
			<td><?php echo $id?></td>
			<td><?php echo $postedby?></td>
			<td class="qty-cell <?php echo $status == 'No' ? 'editable-cell' : ''; ?>">
				<div id="pcount_<?php echo $n?>" class="cell-editor" <?php echo $pcounteditable?>><?php echo $pcount?></div>
			</td>
			<td class="remarks-cell <?php echo $status == 'No' ? 'editable-cell' : ''; ?>">
				<div id="remarks_<?php echo $n?>" title="<?php echo $remarks?>" class="cell-editor" <?php echo $pcounteditable?>><?php echo $function->limitStringLength($remarks, 20)?></div>
			</td>
			
			
			<td style="width:130px;">
				<?php
					if($status=='No'){
				?>
				<div class="action-stack">
				<?php 
						if($function->GetPcountDataInventoryChecker($transdate,$db) == '0'){
				?>

					
						<button type="button" class="btn btn-success btn-sm" style="width:100px" onclick="receiveThis('<?php echo $n?>','<?php echo $id?>','<?php echo $itemcode?>','<?php echo $category?>','<?php echo $itemdescription?>','<?php echo $qty?>','<?php echo $reportdate?>','<?php echo $shift?>')">
							Receive?&nbsp;<i class="fa fa-sign-in" aria-hidden="true"></i>
						</button>
				<?php
						}
				?>	
					
					
					<button class="btn btn-danger btn-sm" style="width:100px" onclick="voidThis('<?php echo $id?>','<?php echo $itemcode?>','<?php echo $category?>','<?php echo $itemdescription?>','<?php echo $qty?>','<?php echo $reportdate?>','<?php echo $shift?>')">
						Void?&nbsp;<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>

				<?php
					} else {
				?>
					<span class="status-pill <?php echo $status == 'Yes' ? 'status-received' : 'status-void'; ?>"><i class="fa <?php echo $statfa?>" style="<?php echo $fastyle?>" aria-hidden="true"></i>&nbsp;
						<?php echo $statData?>
					</span>
				
				<?php	
					}
				?>
			</td>
		</tr>
<?php 
		} 
	} else { 
?>
		<tr>
			<td colspan="12" style="text-align:center"><i class="fa fa-bell"></i>&nbsp;&nbsp;No Records</td>
		</tr>
<?php } ?>
	</tbody>
</table>
</div>

<?php
	if($function->GetProductionifExist($transdate, $db) == 1){
?>
	<div class="receiving-section">
		<div class="section-title">Yield & Reconciliation Summary</div>
		<table style="width: 100%" class="table table-bordered table-striped professional-table">
		<thead>
			<tr class="summary-head">
				<th colspan="5" style="text-align:center">BAKER</th>
				<th colspan="3" style="text-align:center">SUPERVISOR</th>
				<th colspan="2" style="text-align:center">REPORTS</th>
			</tr>
			<tr>
				<th style="text-align:center">#</th>
				<th style="text-align:center">TIME</th>
				<th style="text-align:center">BTCH NO.</th>
				<th style="text-align:center">ITEM DESCRIPTION</th>
				<th style="text-align:center">BAKER'S YIELD</th>
				
				<th style="text-align:center">ACTUAL PRODUCED</th>
				<th style="text-align:center">CHEKED & ACKNOWLEDGE BY</th>
				<th style="text-align:center">REMARKS</th>
				
				<th style="text-align:center">CHARGE</th>
				<th style="text-align:center">OVER YIELDING</th>

			</tr>
		</thead>
		<tbody>
		<?php
	
		$sqlQuery = "SELECT DISTINCT item_description,item_code,created_time,batch_received,quantity_received,charge,over_yield,confirmed_by,receiving_detail_id FROM dbc_dbc_production WHERE report_date='$transdate' AND status='Yes' ORDER BY receiving_detail_id DESC";
		$results = mysqli_query($db, $sqlQuery); 
		   
	    if ( $results->num_rows > 0 ) 
	    {
	    	$i = $chargeTotal = $overyieldTotal = 0;
	    	while($RECVROW = mysqli_fetch_array($results))  
			{
				$i++;
				$itemcode = $RECVROW['item_code'];
				$receivingdetailid = $RECVROW['receiving_detail_id'];
				
				$createdtime = $RECVROW['created_time'];
				$batchreceived = $RECVROW['batch_received'];
				$itemname = $RECVROW['item_description'];
				$quantityreceived = $RECVROW['quantity_received'];
				$confirmedby = $RECVROW['confirmed_by'];
				
				$charge = $RECVROW['charge'];
				$chargeTotal += $charge;
				$overyield = $RECVROW['over_yield'];
				$overyieldTotal += $overyield;

				$pcount = $function->GetDataByRecevingId('pcount','dbc_fgts_pcount',$receivingdetailid,$db);
				$remarks = $function->GetDataByRecevingId('remarks','dbc_fgts_pcount',$receivingdetailid,$db);
				
				$variance = $pcount - $quantityreceived;
	
				$varstyle = $pcount != $quantityreceived ? 'color:#dc3545;' : '';	
				?>
				
					<tr>
						<td><?php echo $i?></td>
						<td><?php echo $createdtime?></td>
						<td><?php echo $batchreceived?></td>
						<td><?php echo $itemname?></td>
						<td style="text-align:right; <?php echo $varstyle?>"><?php echo $quantityreceived?></td>
						
						<td style="text-align:right; <?php echo $varstyle?>"><?php echo $pcount?></td>
						<td style="text-align:center"><?php echo $confirmedby?></td>
						<td title="<?php echo $remarks?>" style="text-align:center"><?php echo $function->limitStringLength($remarks, 20)?></td>
						
						<td style="text-align:right"><?php echo $charge?></td>
						<td style="text-align:right"><?php echo $overyield?></td>

					</tr>
				
				<?php
			}
			?>
				<tr class="totals-row">
					<td colspan="8">TOTAL:</td>
					<td style="text-align:right"><?php echo number_format($chargeTotal,2)?></td>
					<td style="text-align:right"><?php echo number_format($overyieldTotal,2)?></td>
				</tr>

			<?php
		}
		?>
		</tbody>
		</table>
	</div>
	
	<?php } ?>

<script>
function voidThis(id,itemcode,category,itemdescription,qty,reportdate,shift)
{
	var mode = 'voiddbc';
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Management/actions/actions.php", { mode: mode, id: id, itemcode: itemcode, category: category, itemdescription: itemdescription, qty: qty, reportdate: reportdate, shift: shift },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});
}
function receiveThis(n,id,itemcode,category,itemdescription,qty,reportdate,shift)
{
	var mode = 'receivedbc';
	
	var pcount = $('#pcount_'+n).text();
	var remarks = $('#remarks_'+n).text();
	
	if(pcount == ''){
		app_alert("System Message", "Required Physical Count", "warning");
		return false;
	}
	if (isNaN(pcount)) {
		app_alert("System Message", "Please enter a valid number for pcount", "warning");
        return false;
    }

	swal({
		title: "Confirm Receive",
		text: "Proceed receiving this item?",
		icon: "warning",
		buttons: ['Cancel', 'Yes, Receive'],
		dangerMode: false,
	}).then(function(isConfirm) {
		if (!isConfirm) {
			return;
		}

		rms_reloaderOn('Loading...');
		$.post("./Modules/DBC_Management/actions/actions.php", { mode: mode, id: id, itemcode: itemcode, category: category, itemdescription: itemdescription, qty: qty, reportdate: reportdate, shift: shift, pcount: pcount, remarks: remarks },
		function(data) {		
			$('#smnavdata').html(data);
			rms_reloaderOff();
		});
	});		
}
</script>
