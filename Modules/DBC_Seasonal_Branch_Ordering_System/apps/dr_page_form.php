<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
define("MODULE_NAME", "DBC_Seasonal_Branch_Ordering_System");
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/".MODULE_NAME."/class/Class.functions.php");
$function = new FDSFunctions;

$drno = $_POST['drno'];
$rowidmain = $_POST['rowidmain'];

$sqlQueryData = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE dr_number='$drno'";
$dataResults = mysqli_query($db, $sqlQueryData);    
if ( $dataResults->num_rows > 0 ) 
{
	$i=0;
	while($DATAROW = mysqli_fetch_array($dataResults))  
	{
		$rowid = $DATAROW['id'];
		$branch = $DATAROW['branch'];
		$mrs_no = $DATAROW['control_no'];
		$recipient = $DATAROW['recipient'];
		$trans_date = $DATAROW['trans_date'];
	}
}

?>




	<table style="width: 100%;font-family:sans-serif" class="table">
		<tr class="table-thth">
			<th colspan="7">MATERIAL REQUISITION FORM</th>
		</tr>
		<tr>
			<td style="width:120px">Requesting Section/Branch:</td>
			<td style="width:5px !important;">&nbsp;</td>
			<td style="border-bottom:1px solid #232323 !important;width:400px"><?php echo $branch;?></td>
			<td style="width:">&nbsp;</td>
			<td>Control No.:</td>
			<td style="width:3px !important;"></td>
			<td style="width:150px;border-bottom:1px solid #232323 !important;text-align:center;color:red;font-weight:bold"><?php echo $mrs_no;?></td>
		</tr>
		<tr>
			<td style="width:120px">DR Number:</td>
			<td style="width:5px !important;">&nbsp;</td>
			<td style="border-bottom:1px solid #232323 !important;width:100px"><?php echo $drno?></td>
			<td style="width:">&nbsp;</td>
			<td>Date:</td>
			<td style="width:3px !important;">&nbsp;</td>
			<td style="width:150px;border-bottom:1px solid #232323 !important;text-align:center"><?php echo $trans_date;?></td>
		</tr>
	</table>
	<table style="width: 100%" class="tabletas">
		<tr>
			<th style="width:40px;text-align:center">#</th>
			<th style="width:80px">Item Code</th>
			<th>Description</th>
			<th style="width:100px">Units (UOM)</th>
			<th style="width:75px">Quantity</th>
			<th style="width:75px;">WH Qty</th>
			<th style="width:75px;">Inv Ending</th>
				<th style="width:75px;">Brch Rcvd</th>
		</tr>
<?php
	$sqlQueryData = "SELECT * FROM dbc_seasonal_branch_mrs_transaction WHERE branch='$branch' AND request_id='$rowid'";
	$dataResults = mysqli_query($db, $sqlQueryData);    
	if ( $dataResults->num_rows > 0 ) 
	{
		$x=0;
		while($DATAROW = mysqli_fetch_array($dataResults))  
		{
			$x++;
			$editid = $DATAROW['id'];
			$remarks = $DATAROW['remarks'];
			$itemcode = $DATAROW['item_code'];
			
			$controlno = $DATAROW['control_no'];
			
			$branchreceived = $DATAROW['branch_received'];
			$branchreceivedstatus = $DATAROW['branch_received_status'];
			
			$remarks = $function->shortenText($remarks,20);
			
			$actionButton = '';
			$branchrcvdstyle = '';	
			$brnchrcvdeditable = '';
			if($branchreceivedstatus == '0'){
				$brnchrcvdeditable = 'contenteditable="true"';
				$branchrcvdstyle = 'background-color:#f7e9d5';
			}
			
?>		
		<tr>
			<td style="text-align:center"><?php echo $x; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['item_code']; ?></td>
			<td><?php echo $DATAROW['item_description']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['uom']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['quantity']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['wh_quantity']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['inv_ending']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['branch_received']; ?></td>			
			
		</tr>
<?php } ?>
		<tr>
			<td colspan="10" style="padding:5px;white-space:normal">
				<span style="font-weight:600">REMARKS:</span> <?php echo $function->getOrderRemarks($mrs_no,$db); ?>
			</td>
		</tr>
<?php } else { ?>		
		<tr>
			<td colspan="10" style="text-align:center">No Items</td>
		</tr>
<?php } ?>	
	</table>



<?php if($function->GetOrderStatus($mrs_no,"order_transit",$db) == 1) { 
	 if($function->GetOrderStatus($mrs_no,"status",$db) == 'Closed') { $rcvdel = 'disabled'; } else { $rcvdel = ''; }
?>


<?php } mysqli_close($db); ?>
<div class="results"></div>
<script>

function testlang(params){
	alert(params);
}

