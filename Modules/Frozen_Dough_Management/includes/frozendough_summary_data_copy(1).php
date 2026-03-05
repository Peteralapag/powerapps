<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
$function = new FDSFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$date = date("Y-m-d");

if(isset($_SESSION['FDS_DATEFROM']) && isset($_SESSION['FDS_DATETO'])) {
    $datefrom = $_SESSION['FDS_DATEFROM'];
    $dateto = $_SESSION['FDS_DATETO'];
} else {
    $datefrom = $_SESSION['FDS_TRANSDATE'];
    $dateto = $_SESSION['FDS_TRANSDATE'];
}

?>
<style>
.table td {
	padding:2px 5px 2px 5px !important;
}
</style>
<div style="overflow-y: auto; max-height: 500px;">
	<table style="width: 100%" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width:50px;text-align:center">#</th>
				<th>ITEM DESCRIPTION</th>
				<th>BEG</th>
				<th>TX-IN</th>
				<th>TOTAL</th>
				<th>TX-OUT</th>
				<th>EXPTD. STKS</th>
				<th>PCOUNT</th>
				<th>VARIANCE</th>
	
				
			</tr>
		</thead>
		<tbody>	
	<?PHP
		$sqlQuery = "SELECT * FROM fds_itemlist";
		$results = mysqli_query($db, $sqlQuery);    
	    if ( $results->num_rows > 0 ) 
	    {
	    	$n=0;
	    	while($RECVROW = mysqli_fetch_array($results))  
			{
				$n++;
				$itemcode = $RECVROW['item_code'];
				$item = $RECVROW['item_description'];
				
				$beg1 = date('Y-m-d', strtotime($datefrom . ' -1 day'));
				$beg = $function->GetPcountData('pcount',$itemcode,$beg1,$beg1,$db);
				//$t_in = $function->GetProductionData('quantity_received',$itemcode,$datefrom,$dateto,$db); 
				$t_in = $function->GetPcountData('pcount',$itemcode,$datefrom,$dateto,$db); 
				$total = $beg + $t_in;
				$t_out = $function->GetTransferOutData($itemcode,$datefrom,$dateto,$db);
				$expectedstk = $total - $t_out;
				$pcount = $function->GetPcountData('pcount',$itemcode,$datefrom,$dateto,$db);
				$variance = $pcount - $expectedstk;
				$varstyle = $variance < 0 ? 'color:#dc3545;' : '';		
				
	?>	
			<tr>
				<td style="text-align:center; height: 25px;"><?php echo $n; ?></td>
				<td><?php echo $item?></td>
				<td style="text-align:right"><?php echo $beg?></td>
				<td style="text-align:right"><?php echo $t_in?></td>
				<td style="text-align:right"><?php echo $total?></td>
				<td style="text-align:right"><?php echo $t_out?></td>
				<td style="text-align:right"><?php echo $expectedstk?></td>
				<td style="text-align:right"><?php echo $pcount?></td>
				<td style="text-align:right; <?php echo $varstyle?>"><?php echo $variance?></td>
				
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
</div>

