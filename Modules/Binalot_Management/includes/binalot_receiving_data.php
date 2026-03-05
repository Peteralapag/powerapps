<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$date = date("Y-m-d");

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    // Validate and sanitize user input for limit
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        // Set session and query limit
        $_SESSION['BINALOT_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        // Handle invalid input
        $limitClause = "";
        $_SESSION['BINALOT_SHOW_LIMIT'] = $limitClause;
        // You might want to set an error message here
    }
} else {
    $limitClause = "";
    $_SESSION['BINALOT_SHOW_LIMIT'] = $limitClause;
}

if(isset($_POST['transdate'])) {
	$transdate = $_POST['transdate'];
	$_SESSION['BINALOT_TRANSDATE'] = $transdate;
} else {
	$transdate = $_SESSION['BINALOT_TRANSDATE'];
}

?>
<style>
.table td {
	padding:2px 5px 2px 5px !important;
}
.lamesa-ko td {
	vertical-align:middle;
}
</style>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
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
	$sqlQuery = "SELECT * FROM binalot_binalot_production WHERE report_date='$transdate' ORDER BY receiving_detail_id DESC";
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
			
			$pcount = $status == 'No'? '' : $function->GetDataByRecevingId('pcount','binalot_fgts_pcount',$id,$db);
			$remarks = $status == 'No'? '' : $function->GetDataByRecevingId('remarks','binalot_fgts_pcount',$id,$db);
			
			$pcountstyle = $status == 'No'? 'background-color:#f7e9d5': '';
			$pcounteditable = $status == 'No'? 'contenteditable="true"': '';
			
?>	
		<tr class="lamesa-ko">
			<td style="text-align:center; height: 25px;"><?php echo $n; ?></td>
			<td><?php echo $reportdate?></td>
			<td><?php echo $createdtime?></td>
			<td>FD RECEPTION AREA</td>
			<td><?php echo $category?></td>
			<td title="<?php echo $itemdescription?>"><?php echo $function->limitStringLength($itemdescription, 30)?></td>
			<td><?php echo $itemcode?></td>
			<td><?php echo $id?></td>
			<td><?php echo $postedby?></td>
			<td id="pcount_<?php echo $n?>" style="text-align:center; <?php echo $pcountstyle?>" <?php echo $pcounteditable?>><?php echo $pcount?></td>
			<td id="remarks_<?php echo $n?>" title="<?php echo $remarks?>" style="text-align:center; <?php echo $pcountstyle?>" <?php echo $pcounteditable?>><?php echo $function->limitStringLength($remarks, 20)?></td>
			
			
			<td style="width:120px; height: 25px; padding:0 !important">
				<?php
					if($status=='No'){
				?>
				
				
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

				<?php
					} else {
				?>
					<span class="form-control form-control-sm color-white" style="text-align:center; <?php echo $spanstyle?>;"><i class="fa <?php echo $statfa?>" style="<?php echo $fastyle?>" aria-hidden="true"></i>&nbsp;
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
	if($function->GetProductionifExist($transdate, $db) == 1){
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
	
		$sqlQuery = "SELECT DISTINCT item_description,item_code,created_time,batch_received,quantity_received,charge,over_yield,confirmed_by,receiving_detail_id FROM binalot_binalot_production WHERE report_date='$transdate' AND status='Yes' ORDER BY receiving_detail_id DESC";
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

				$pcount = $function->GetDataByRecevingId('pcount','binalot_fgts_pcount',$receivingdetailid,$db);
				$remarks = $function->GetDataByRecevingId('remarks','binalot_fgts_pcount',$receivingdetailid,$db);
				
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
	var mode = 'voidbinalot';
	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, id: id, itemcode: itemcode, category: category, itemdescription: itemdescription, qty: qty, reportdate: reportdate, shift: shift },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});
}
function receiveThis(n,id,itemcode,category,itemdescription,qty,reportdate,shift)
{
	var mode = 'receivebinalot';
	
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
	

	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, id: id, itemcode: itemcode, category: category, itemdescription: itemdescription, qty: qty, reportdate: reportdate, shift: shift, pcount: pcount, remarks: remarks },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});		
}
</script>
