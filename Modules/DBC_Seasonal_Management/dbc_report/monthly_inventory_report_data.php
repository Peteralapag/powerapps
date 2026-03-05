<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.inventory.php";
$function = new DBCFunctions;
$inventory = new DBCInventory;

$_SESSION['DBC_SEASONAL_SUMMARY_DATEFROM'] = $_POST['dateFrom'];
$_SESSION['DBC_SEASONAL_SUMMARY_DATETO'] = $_POST['dateTo'];

$datefrom = $_POST['dateFrom'];
$dateto = $_POST['dateTo'];

$startDate = new DateTime($datefrom);
$endDate = new DateTime($dateto);

$interval = $startDate->diff($endDate);

$numDays = $interval->days + 1;
$dayscount = 0;

$month_name = date('F j, Y', strtotime($datefrom)). ' - ' .date('F j, Y', strtotime($dateto));
for ($day = 1; $day <= $numDays; $day++) {
    $dayscount++;
}

$formattedDate = date('F j, Y', strtotime($datefrom));

?>
<style>

.sticky-column {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    background-color: #a4cfc8;
    z-index: 2 !important;
    white-space: nowrap;
}

.sticky-column-1 {
    left: 0;
    z-index: 1;
}
.sticky-column-1-data {
    left: 0;
    background:white !important;
}

.sticky-column-2 {
    left: 35px;
}
.sticky-column-2-data {
    left: 35px;
    background:white !important;
}

.sticky-column-3 {
    left: 140px;
}
.sticky-column-3-data {
    left: 140px;
    background:white !important;
}

.sticky-column-4 {
    left: 405px;
}
.sticky-column-4-data {
    left: 405px;
    background:white !important;
}

.sticky-column-5{
    left: 481px;
}
.sticky-column-5-data {
    left: 481px;
    background:white !important;
}

.sticky-header {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #f5f5f5;
    z-index: 3 !important;
}


</style>
<table style="width:100%" class="table table-bordered table-striped">
	<thead>
		<tr class="border-report-title">			
			<th colspan="5" style="text-align:center" class="bg-primary sticky-column sticky-column-1">INVENTORY <?php echo strtoupper($month_name)?></th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-secondary">INVENTORY IN</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-info">INVENTORY RETURN</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-success">INVENTORY DAMAGE</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#153515">INVENTORY COMPLEMENTARY</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#433409">INVENTORY BAD ORDER</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#344865">INVENTORY CHARGES</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#736333">INVENTORY R &amp; D</th>			
			
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-warning">INVENTORY OUT</th>

			<th colspan="8" style="text-align:center;color:#fff" class="bg-danger">TOTAL SUMMARY</th>
		</tr>
		<tr>
			<th class="bg-primary sticky-column sticky-column-1 sticky-header" style="width:50px">#</th>
			<th class="bg-primary sticky-column sticky-column-2 sticky-header" style="width:70px">ITEM CODE</th>
			<th class="bg-primary sticky-column sticky-column-3 sticky-header">ITEM NAME</th>
			<th class="bg-primary sticky-column sticky-column-4 sticky-header">UOM</th>
			<th class="bg-primary sticky-column sticky-column-5 sticky-header" style="width:70px">BEGINNING</th>
		<?php
			for ($tin = 1; $tin <= $numDays; $tin++)
			{				
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($tin - 1) . ' days');
		        echo '<th style="text-align:center;width:50px; background-color:#707070">Day ' . $dateObject->format('j') . '</th>';
			}	
		?>
		
			<th style="width:70px;text-align:center; background-color:#707070">TOTAL</th>
		
		<?php
			for ($return = 1; $return <= $numDays; $return++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($return - 1) . ' days');
		        echo '<th style="text-align:center;width:50px; background-color:#0dcaf0">Day ' . $dateObject->format('j') . '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#0dcaf0">TOTAL</th>

		<?php
			for ($damage = 1; $damage <= $numDays; $damage++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($damage - 1) . ' days');
		        echo '<th style="text-align:center;width:50px; background-color:#198754">Day ' . $dateObject->format('j') . '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#198754">TOTAL</th>

		<?php
			for ($complementary = 1; $complementary <= $numDays; $complementary++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($complementary - 1) . ' days');
		        echo '<th style="text-align:center;width:50px; background-color:#153515">Day ' . $dateObject->format('j') . '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#153515">TOTAL</th>

		<?php
			for ($badorder = 1; $badorder <= $numDays; $badorder++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($badorder - 1) . ' days');
		        echo '<th style="text-align:center;width:50px; background-color:#433409">Day ' . $dateObject->format('j') . '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#433409">TOTAL</th>

		<?php
			for ($charges = 1; $charges <= $numDays; $charges++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($charges - 1) . ' days');
		        echo '<th style="text-align:center;width:50px; background-color:#344865">Day ' . $dateObject->format('j') . '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#344865">TOTAL</th>

		<?php
			for ($rnd = 1; $rnd <= $numDays; $rnd++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($rnd - 1) . ' days');
		        echo '<th style="text-align:center;width:50px; background-color:#736333">Day ' . $dateObject->format('j') . '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#736333">TOTAL</th>

		
		<?php
			for ($tout = 1; $tout <= $numDays; $tout++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($tout - 1) . ' days');
		        echo '<th style="text-align:center;width:50px" class="bg-warning">Day ' . $dateObject->format('j') . '</th>';
			}		
		?>
			<th  class="bg-warning">TOTAL OUT</th>	
			<th class="bg-danger">EXPTD. STKS</th>
			<th class="bg-danger">PHY. COUNT</th>
			<th class="bg-danger">VARIANCES</th>
			<th class="bg-danger">UNIT PRICE</th>
			<th class="bg-danger">VAR. AMOUNT</th>
			<th class="bg-danger">SHORT</th>
			<th class="bg-danger">OVER</th>
			
			<!--th class="color-total-th">BKR. CHRGS</th-->
		</tr>
	</thead>
	<tbody>
<?php
	$sqlQuery = "SELECT * FROM dbc_seasonal_itemlist WHERE active=1";
	$results = mysqli_query($db, $sqlQuery);
	if ($results->num_rows > 0) {
    $i = 0;
    while ($INVROW = mysqli_fetch_array($results))
    {
	    $i++;
	    $itemcode = $INVROW['item_code'];
	    $itemdescription = $INVROW['item_description'];    
	    $unitprice = $INVROW['unit_price'];
	    
		$begdate = $datefrom;
		$beg1 = date('Y-m-d', strtotime($begdate. ' -1 day'));
		$beginning = $function->GetPcountBeginning('p_count',$itemcode,$beg1,$db);			
?>	
		<tr>
			<td style="text-align:center" class="sticky-column sticky-column-1-data"><?php echo $i; ?></td> 
			<td style="text-align:center" class="sticky-column sticky-column-2-data"><?php echo $itemcode; ?></td>
			<td title="<?php echo $itemdescription?>" class="sticky-column sticky-column-3-data"><?php echo $itemdescription ?></td>
			<td style="text-align:center" class="sticky-column sticky-column-4-data"><?php echo $INVROW['uom']; ?></td>
			<td style="text-align:right;" class="sticky-column sticky-column-5-data"><?php echo $beginning?></td>		
			
<?php
		$TotalGetInventoryIn=$TotalGetInventoryOut=$TotalGetInventoryReturn=$TotalGetInventoryDamage=$TotalGetInventoryComplementary=$TotalGetInventoryBadorder=$TotalGetInventoryCharges=$TotalGetInventoryRnd=0;
		//IN
		for ($dateLoopIn = clone $startDate; $dateLoopIn <= $endDate; $dateLoopIn->modify('+1 day')) {
			
			$datetin = $dateLoopIn->format('Y-m-d');
			$GetInventoryIn = $inventory->GetInventoryIn($datetin,$itemcode,$db);
			$TotalGetInventoryIn +=	$GetInventoryIn;

?>
	    	<td style="text-align:right" class="psahovin" ondblclick="timeInDetails('<?php echo $itemcode?>','<?php echo $datetin?>')"><?php echo $GetInventoryIn?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryIn,2); ?></td>
		
<?php
		//RETURN
		for ($dateLoopReturn = clone $startDate; $dateLoopReturn <= $endDate; $dateLoopReturn->modify('+1 day')) {
		
			$datereturn = $dateLoopReturn->format('Y-m-d');	
			$GetInventoryReturn = $inventory->GetInventoryOtherData('return','quantity',$datereturn, $itemcode, $db);
			$TotalGetInventoryReturn +=	$GetInventoryReturn;
?>
	    	<td style="text-align:right" class="psahovreturn" ondblclick="otherDetails('return','INVENTORY RETURN','<?php echo $itemcode?>','<?php echo $datereturn?>')"><?php echo $GetInventoryReturn?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryReturn,2); ?></td>
<?php
		//DAMAGE
		for ($dateLoopDamage = clone $startDate; $dateLoopDamage <= $endDate; $dateLoopDamage->modify('+1 day')) {

			$datedamage = $dateLoopDamage->format('Y-m-d');	
			$GetInventoryDamage = $inventory->GetInventoryOtherData('damage','quantity',$datedamage, $itemcode, $db);
			$TotalGetInventoryDamage +=	$GetInventoryDamage;
?>
	    	<td style="text-align:right" class="psahovdamage" ondblclick="otherDetails('damage','INVENTORY DAMAGE','<?php echo $itemcode?>','<?php echo $datedamage?>')"><?php echo $GetInventoryDamage?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryDamage,2); ?></td>
<?php
		//COMPLEMENTARY
		for ($dateLoopComplementary = clone $startDate; $dateLoopComplementary <= $endDate; $dateLoopComplementary->modify('+1 day')) {
			$datecomplementary = $dateLoopComplementary->format('Y-m-d');
			$GetInventoryComplementary = $inventory->GetInventoryOtherData('complementary','quantity',$datecomplementary, $itemcode, $db);
			$TotalGetInventoryComplementary +=	$GetInventoryComplementary;
?>
	    	<td style="text-align:right" class="psahovcomplementary" ondblclick="otherDetails('complementary','INVENTORY COMPLEMENTARY','<?php echo $itemcode?>','<?php echo $datecomplementary?>')"><?php echo $GetInventoryComplementary?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryComplementary,2); ?></td>
<?php
		//BADORDER
		for ($dateLoopBadorder = clone $startDate; $dateLoopBadorder <= $endDate; $dateLoopBadorder->modify('+1 day')) {
			$datebadorder = $dateLoopBadorder->format('Y-m-d');	
			$GetInventoryBadorder = $inventory->GetInventoryOtherData('badorder','quantity',$datebadorder, $itemcode, $db);
			$TotalGetInventoryBadorder +=	$GetInventoryBadorder;
?>
	    	<td style="text-align:right" class="psahovbadorder" ondblclick="otherDetails('badorder','INVENTORY BAD ORDER','<?php echo $itemcode?>','<?php echo $datebadorder?>')"><?php echo $GetInventoryBadorder?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryBadorder,2); ?></td>

<?php
		//CHARGES
		for ($dateLoopCharges = clone $startDate; $dateLoopCharges <= $endDate; $dateLoopCharges->modify('+1 day')) {
			$datecharges = $dateLoopCharges->format('Y-m-d');
			$GetInventoryCharges = $inventory->GetInventoryOtherData('charges','quantity',$datecharges, $itemcode, $db);
			$TotalGetInventoryCharges +=	$GetInventoryCharges;
?>
	    	<td style="text-align:right" class="psahovcharges" ondblclick="otherDetails('charges','INVENTORY CHARGES','<?php echo $itemcode?>','<?php echo $datecharges?>')"><?php echo $GetInventoryCharges?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryCharges,2); ?></td>
<?php
		//RND
		for ($dateLoopRnd = clone $startDate; $dateLoopRnd <= $endDate; $dateLoopRnd->modify('+1 day')) {
			$daternd = $dateLoopRnd->format('Y-m-d');
			$GetInventoryRnd = $inventory->GetInventoryOtherData('rnd','quantity',$daternd, $itemcode, $db);
			$TotalGetInventoryRnd +=	$GetInventoryRnd;
?>
	    	<td style="text-align:right" class="psahovrnd" ondblclick="otherDetails('rnd','INVENTORY R&amp;D','<?php echo $itemcode?>','<?php echo $daternd?>')"><?php echo $GetInventoryRnd?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryRnd,2); ?></td>	

<?php 
		//OUT
		for ($dateLoopOut = clone $startDate; $dateLoopOut <= $endDate; $dateLoopOut->modify('+1 day')) {
			$dateout = $dateLoopOut->format('Y-m-d');	
			$GetInventoryOut = $inventory->GetTransferOutDataVer2($itemcode,$dateout,$db);			
			$TotalGetInventoryOut += $GetInventoryOut;
?>
	    	<td style="text-align:right" class="psahovout" ondblclick="timeOutDetails('<?php echo $itemcode?>','<?php echo $dateout?>')"><?php echo $GetInventoryOut?></td>
	    	
<?php	}
		
		
		$pcountdate = $dateto;

		$pcount = $inventory->GetPcountDataInventory('p_count',$itemcode,$pcountdate,$db);
		
		$firstday = $datefrom;
		$lastday = $dateto;
		
		$charges = $function->GetProductionCharges('charge', $itemcode, $firstday, $db);
		
		$batch = $function->GetProductionData('batch_received',$itemcode,$firstday,$lastday,$db);
		$expectedstocks = ($beginning+$TotalGetInventoryIn+$TotalGetInventoryReturn)-$TotalGetInventoryDamage-$TotalGetInventoryComplementary-$TotalGetInventoryBadorder-$TotalGetInventoryCharges-$TotalGetInventoryRnd-$TotalGetInventoryOut;
		
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
function otherDetails(table,title,itemcode,transdate){
	
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
	
	$('#modaltitle').html(title+" "+dateInWords);
	$.post("./Modules/DBC_Seasonal_Management/apps/inventory_others_form.php", { itemcode: itemcode, transdate: transdate, table: table },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
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
	$.post("./Modules/DBC_Seasonal_Management/apps/inventory_out_form.php", { itemcode: itemcode, transdate: transdate },
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
	$.post("./Modules/DBC_Seasonal_Management/apps/inventory_in_form.php", { itemcode: itemcode, transdate: transdate },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}

</script>
