<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.inventory.php";
$function = new FDSFunctions;
$inventory = new FDSInventory;
$_SESSION['FDS_MONTH'] = $_POST['month'];
$_SESSION['FDS_YEAR'] = $_POST['year'];
$_SESSION['FDS_WEEK'] = $_POST['week'];
$recipient = $_POST['recipient'];
if(isset($_POST['search']) && $_POST['search'] != '')
{
	$search = $_POST['search'];
	$q = "AND item_description LIKE '%$search%' OR item_code LIKE '%$search%'";
} else {
	$q = '';
}
$year = $_POST['year']; 
$month = $_POST['month']; 
$month_name = date("F", strtotime($year."-".$month));


$dateYearMonth = $year.'-'.$month;
$dayscount = 0;
$numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

for ($day = 1; $day <= $numDays; $day++) {
    $date = $dateYearMonth . '-' . sprintf('%02d', $day);
    $dayscount++;
}

?>
<style>
.freeze-column {
    position: sticky;
    top: 0;
    z-index: 1;
}

.psahovin {
	cursor:pointer;
}
.psahoverin:hover{
	color:white;
}

.psahovin:active {
	background-color: #707070;
	color:#fff;
}

.psahovout {
	cursor:pointer;
}
.psahoverout:hover{
	color:white;
}

.psahovout:active {
	background-color: #fce5a1;
	color:#fff;
}

.table td, .table th {border: 1px solid #232323 !important;}
.color-inv-th { background:#78adfb !important }
.color-del-th {	background:#fbdb9f !important;color:#232323 !important;}
.color-ttl-th {	background:#fbcd76 !important;color:#232323 !important;}
.color-invout-th { background:#fce5a1 !important;color:#232323 !important;}
.color-total-th { background:#db858d !important; }
.border-report-title th { border-bottom:3px solid #636363; }
.footer-values td {background:#cecece;text-align:right;border-top:3px solid #232323;border-bottom:1px solid #232323;}
</style>
<table style="width:100%" class="table table-bordered table-striped">
	<thead>
		<tr class="border-report-title">			
			<th colspan="5" style="text-align:center;color:#fff" class="bg-primary freeze-column">INVENTORY</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-secondary"><?php echo strtoupper($month_name); ?> INVENTORY IN</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-warning"><?php echo strtoupper($month_name); ?> INVENTORY OUT</th>
			<th colspan="8" style="text-align:center;color:#fff" class="bg-danger">TOTAL SUMMARY</th>
		</tr>
		<tr>
			<td style="width:50px !important;text-align:center;background:#aeaeae">#</td>
			<th style="width:70px" class="color-inv-th">ITEM CODE</th>
			<th class="color-inv-th">ITEM NAME</th>
			<th class="color-inv-th">UOM</th>
			<th style="width:70px" class="color-inv-th">BEGINNING</th>
		<?php
			for ($tin = 1; $tin <= $numDays; $tin++)
			{
				echo '<th style="text-align:center;width:50px; background-color:#707070">Day ' . $tin. '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#707070">TOTAL</th>
		<?php
			for ($tout = 1; $tout <= $numDays; $tout++)
			{
				echo '<th style="text-align:center;width:50px" class="color-invout-th">Day ' . $tout. '</th>';
			}		
		?>
			<th class="color-invout-th">TOTAL OUT</th>
			
			<th class="color-total-th">EXPTD. STKS</th>
			<th class="color-total-th">PHY. COUNT</th>
			<th class="color-total-th">VARIANCES</th>
			<th class="color-total-th">UNIT PRICE</th>
			<th class="color-total-th">VAR. AMOUNT</th>
			<th class="color-total-th">SHORT</th>
			<th class="color-total-th">OVER</th>
			
			<!--th class="color-total-th">BKR. CHRGS</th-->
		</tr>
	</thead>
	<tbody>
<?php
	$sqlQuery = "SELECT * FROM fds_itemlist WHERE recipient='$recipient' $q";
	$results = mysqli_query($db, $sqlQuery);
	if ($results->num_rows > 0) {
    $i = 0;
    while ($INVROW = mysqli_fetch_array($results))
    {
	    $i++;
	    $itemcode = $INVROW['item_code'];	    
	    $unitprice = $INVROW['unit_price'];
	    
//	    $beginning = $inventory->getInventoryBeginning($cnt_start,$cnt_end,$days_cnt,$col,$itemcode,$month,$year,$db);
		$begdate = $dateYearMonth.'-01';
		$beg1 = date('Y-m-d', strtotime($begdate. ' -1 day'));
		$beginning = $function->GetPcountBeginning('p_count',$itemcode,$beg1,$db);		
		
?>	
		<tr>
			<td style="text-align:center;background:#aeaeae"><?php echo $i; ?></td>
			<td style="text-align:center"><?php echo $itemcode; ?></td>
			<td><?php echo $INVROW['item_description']; ?></td>
			<td style="text-align:center"><?php echo $INVROW['uom']; ?></td>
			<td style="text-align:right;"><?php echo $beginning?></td>
<?php
		$TotalGetInventoryIn=$TotalGetInventoryOut=0;
		for($z=1; $z<= $numDays; $z++){ 
			$datetin = date("Y-m-d", strtotime($year."-".$month."-".$z));	
			$GetInventoryIn = $inventory->GetInventoryIn($datetin,$itemcode,$db);
			$TotalGetInventoryIn +=	$GetInventoryIn;
?>
	    	<td style="text-align:right" class="psahovin" ondblclick="timeInDetails('<?php echo $itemcode?>','<?php echo $datetin?>')"><?php echo $GetInventoryIn?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryIn,2); ?></td>

<?php 
		for($y=1; $y<= $numDays; $y++){ 
			$datetout = date("Y-m-d", strtotime($year."-".$month."-".$y));	
			$GetInventoryOut = $inventory->GetTransferOutData($itemcode,$datetout,$db);
			$TotalGetInventoryOut += $GetInventoryOut;
?>
	    	<td style="text-align:right" class="psahovout" ondblclick="timeOutDetails('<?php echo $itemcode?>','<?php echo $datetout?>')"><?php echo $GetInventoryOut?></td>
<?php	}
		
		
		$lastdateofthemonth = $function->getLastDayOfMonth($year,$month);
		$pcountdate = $dateYearMonth.'-'.$lastdateofthemonth;
		//$pcount = $function->GetPcountData('pcount',$itemcode,$pcountdate,$pcountdate,$db);
		$pcount = $inventory->GetPcountDataInventory('p_count',$itemcode,$pcountdate,$db);
		
		$firstday = $dateYearMonth.'-01';
		$lastday = $dateYearMonth.'-'.$function->getLastDayOfMonth($year,$month);
		
		$charges = $function->GetProductionCharges('charge', $itemcode, $firstday, $db);
		
		$batch = $function->GetProductionData('batch_received',$itemcode,$firstday,$lastday,$db);
		$expectedstocks = ($beginning + $TotalGetInventoryIn) - $TotalGetInventoryOut;
		
		$variance = $pcount - $expectedstocks;
		$varianceAmount = $unitprice * $variance;
		
		$variancestyle = $variance < 0 ? 'color:#DD2F6E': '';
		
		if($variance < 0){
			$variancestyle = 'color:#dc3545';
			$short = $variance;
			$over = 0;
		}
		else {
			$variancestyle = '';
			$short = 0;
			$over = $variance;
		}	

		
		
?>					
			<td style="text-align:right"><?php echo number_format($TotalGetInventoryOut,2); ?></td>
					
			<td style="text-align:right"><?php echo number_format($expectedstocks,2); ?></td>
			<td style="text-align:right"><?php echo number_format($pcount,2)?></td>
			<td style="text-align:right;<?php echo $variancestyle?>"><?php echo number_format($variance,2); ?></td>
			
			<td style="text-align:right"><?php echo $unitprice ?></td>
			<td style="text-align:right"><?php echo number_format($varianceAmount,2); ?></td>
			<td style="text-align:right"><?php echo number_format($short,2); ?></td>
			<td style="text-align:right"><?php echo number_format($over,2); ?></td>
			
			<!--td style="text-align:right"><?php echo $charges?></td-->
		</tr>
<?php 
	}

?>
		
		
<?php } else { ?>		
		<tr>
			<td colspan="50" style="text-align:center;color:#fff" class="bg-primary"><i class="fa fa-bell color-orange"></i> No Record(s) found.</td>
		</tr>
<?php } ?>
	</tbody>
</table>

<script>
function timeOutDetails(itemcode,transdate){
	
	const dateStr = transdate;
	const dateObj = new Date(dateStr);
	
	const months = [
	  "January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];
		
	const year = dateObj.getFullYear();
	const month = months[dateObj.getMonth()];
	const dayOfMonth = dateObj.getDate();
	
	const dateInWords = `${month} ${dayOfMonth}, ${year}`;
	
	$('#modaltitle').html("INVENTORY OUT "+dateInWords);
	$.post("./Modules/Frozen_Dough_Management/apps/inventory_out_form.php", { itemcode: itemcode, transdate: transdate },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function timeInDetails(itemcode,transdate){
		
	const dateStr = transdate;
	const dateObj = new Date(dateStr);
	
	const months = [
	  "January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];
		
	const year = dateObj.getFullYear();
	const month = months[dateObj.getMonth()];
	const dayOfMonth = dateObj.getDate();
	
	const dateInWords = `${month} ${dayOfMonth}, ${year}`;
	
	$('#modaltitle').html("INVENTORY IN "+dateInWords);
	$.post("./Modules/Frozen_Dough_Management/apps/inventory_in_form.php", { itemcode: itemcode, transdate: transdate },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});

}
</script>
