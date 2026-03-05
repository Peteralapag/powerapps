<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$date = date("Y-m-d");

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    // Validate and sanitize user input for limit
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        // Set session and query limit
        $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        // Handle invalid input
        $limitClause = "";
        $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = $limitClause;
        // You might want to set an error message here
    }
} else {
    $limitClause = "";
    $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = $limitClause;
}

if(isset($_POST['transdate'])) {
	$transdate = $_POST['transdate'];
	$_SESSION['DBC_SEASONAL_TRANSDATE'] = $transdate;
} else {
	$transdate = $_SESSION['DBC_SEASONAL_TRANSDATE'];
}

?>
<style>
.table td {
	padding:2px 5px 2px 5px !important;
}
</style>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th style="width:50px;text-align:center">#</th>
			<th>REPORT DATE</th>
			<th>CTRL NO.</th>
			<th>SUPPLIER</th>
			<th>CATEGORY</th>
			<th>ITEM DESCRIPTION</th>
			<th>ITEM CODE</th>
			<th>QUANTITY</th>
			<th>CREATED BY</th>
			<th>CHECKED BY</th>
			<th>ACTIONS</th>
			
		</tr>
	</thead>
	<tbody>	
<?PHP
	$sqlQuery = "SELECT * FROM dbc_seasonal_dbc_production WHERE report_date='$transdate' ORDER BY receiving_detail_id DESC";
	$results = mysqli_query($db, $sqlQuery); 
	$n=0;   
    if ( $results->num_rows > 0 ) 
    {
    	while($RECVROW = mysqli_fetch_array($results))  
		{
			$n++;
			$id = $RECVROW['receiving_detail_id'];
			
			$reportdate = $RECVROW['report_date'];
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
?>	
		<tr style="vertical-align:middle">
			<td style="text-align:center; height: 25px;"><?php echo $n; ?></td>
			<td style="height: 25px"><?php echo $reportdate?></td>
			<td style="height: 25px"><?php echo $id?></td>
			<td style="height: 25px">DAVAO BAKING CENTER AREA</td>
			<td style="height: 25px"><?php echo $category?></td>
			<td style="height: 25px"><?php echo $itemdescription?></td>
			<td style="height: 25px"><?php echo $itemcode?></td>
			<td style="height: 25px"><?php echo $qty?></td>
			<td style="height: 25px"><?php echo $postedby?></td>
			<td style="height: 25px"><?php echo $confirmedby?></td>
			
			
			<td style="width:120px; height: 25px;">
				<?php
					if($status=='No'){
				?>
					<span class="form-control form-control-sm" style="text-align:center">PENDING</span>

				<?php
					} else {
				?>
					<span class="form-control form-control-sm" style="text-align:center; <?php echo $spanstyle?>"><i class="fa <?php echo $statfa?>" style="<?php echo $fastyle?>" aria-hidden="true"></i>&nbsp;
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
			<td colspan="9" style="text-align:center"><i class="fa fa-bell"></i>&nbsp;&nbsp;No Records</td>
		</tr>
<?php } ?>
	</tbody>
</table>

<?php
	if($function->GetProductionifExist($transdate,$db) == 1){
?>
<hr>
<table style="width: 100%" class="table table-bordered table-striped">
	<thead>
		<tr>
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

	$sqlQuery = "SELECT DISTINCT item_description,item_code,created_time,batch_received,quantity_received,charge,over_yield,confirmed_by,receiving_detail_id FROM dbc_seasonal_dbc_production WHERE report_date='$transdate' AND status='Yes' ORDER BY receiving_detail_id DESC";
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
			
			$pcount = $function->GetDataByRecevingId('pcount','dbc_seasonal_fgts_pcount',$receivingdetailid,$db);
			
			$remarks = $function->GetDataByRecevingId('remarks','dbc_seasonal_fgts_pcount',$receivingdetailid,$db);
			
			$beg1 = date('Y-m-d', strtotime($transdate . ' -1 day'));
			$beg = $function->GetPcountData('pcount',$itemcode,$beg1,$beg1,$db);


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
					<td style="text-align:center"><?php echo $remarks?></td>
					
					<td style="text-align:right"><?php echo $charge?></td>
					<td style="text-align:right"><?php echo $overyield?></td>
				</tr>
			
			<?php
		}
		?>
			<tr style="background-color:silver">
				<td colspan="8">TOTAL:</td>
				<td style="text-align:right"><?php echo number_format($chargeTotal,2)?></td>
				<td style="text-align:right"><?php echo number_format($overyieldTotal,2)?></td>
			</tr>
		<?php
	}
	?>
	</tbody>
</table>

<?php } ?>

<script>
function voidThis(id,itemcode,category,itemdescription,qty,reportdate,shift)
{
	var mode = 'voiddbc';
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, id: id, itemcode: itemcode, category: category, itemdescription: itemdescription, qty: qty, reportdate: reportdate, shift: shift },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});
}
function receiveThis(id,itemcode,category,itemdescription,qty,reportdate,shift)
{
	var mode = 'receivedbc';
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, id: id, itemcode: itemcode, category: category, itemdescription: itemdescription, qty: qty, reportdate: reportdate, shift: shift },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});		
}
</script>
