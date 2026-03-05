<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";
$function = new DBCFunctions;
$inventory = new DBCInventory;
$_SESSION['DBC_MONTH'] = $_POST['month'];
$_SESSION['DBC_YEAR'] = $_POST['year'];
$_SESSION['DBC_WEEK'] = $_POST['week'];
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
    left: 478px;
}
.sticky-column-4-data {
    left: 478px;
    background:white !important;
}



.sticky-column-5{
    left: 577px;
}
.sticky-column-5-data {
    left: 577px;
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
			<th colspan="5" style="text-align:center" class="bg-primary sticky-column sticky-column-1">INVENTORY</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-secondary"><?php echo strtoupper($month_name); ?> INVENTORY IN</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-info"><?php echo strtoupper($month_name); ?> INVENTORY RETURN</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-success"><?php echo strtoupper($month_name); ?> INVENTORY DAMAGE</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#153515"><?php echo strtoupper($month_name); ?> INVENTORY COMPLEMENTARY</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#433409"><?php echo strtoupper($month_name); ?> INVENTORY BAD ORDER</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#344865"><?php echo strtoupper($month_name); ?> INVENTORY CHARGES</th>
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff;background-color:#736333"><?php echo strtoupper($month_name); ?> INVENTORY R &amp; D</th>			
			
			<th colspan="<?php echo $dayscount+1?>" style="text-align:center;color:#fff" class="bg-warning"><?php echo strtoupper($month_name); ?> INVENTORY OUT</th>

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
				echo '<th style="text-align:center;width:50px; background-color:#707070">Day ' . $tin. '</th>';
			}	
		?>
		
			<th style="width:70px;text-align:center; background-color:#707070">TOTAL</th>
		
		<?php
			for ($return = 1; $return <= $numDays; $return++)
			{
				echo '<th style="text-align:center;width:50px; background-color:#0dcaf0">Day ' . $return. '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#0dcaf0">TOTAL</th>

		<?php
			for ($damage = 1; $damage <= $numDays; $damage++)
			{
				echo '<th style="text-align:center;width:50px; background-color:#198754">Day ' . $damage. '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#198754">TOTAL</th>

		<?php
			for ($complementary = 1; $complementary <= $numDays; $complementary++)
			{
				echo '<th style="text-align:center;width:50px; background-color:#153515">Day ' . $complementary. '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#153515">TOTAL</th>

		<?php
			for ($badorder = 1; $badorder <= $numDays; $badorder++)
			{
				echo '<th style="text-align:center;width:50px; background-color:#433409">Day ' . $badorder. '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#433409">TOTAL</th>

		<?php
			for ($charges = 1; $charges <= $numDays; $charges++)
			{
				echo '<th style="text-align:center;width:50px; background-color:#344865">Day ' . $charges. '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#344865">TOTAL</th>

		<?php
			for ($rnd = 1; $rnd <= $numDays; $rnd++)
			{
				echo '<th style="text-align:center;width:50px; background-color:#736333">Day ' . $rnd. '</th>';
			}	
		?>
			<th style="width:70px;text-align:center; background-color:#736333">TOTAL</th>

		
		<?php
			for ($tout = 1; $tout <= $numDays; $tout++)
			{
				echo '<th style="text-align:center;width:50px" class="bg-warning">Day ' . $tout. '</th>';
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
	$sqlQuery = "SELECT * FROM dbc_itemlist WHERE recipient='$recipient' $q";
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
			<td style="text-align:center" class="sticky-column sticky-column-1-data"><?php echo $i; ?></td> 
			<td style="text-align:center" class="sticky-column sticky-column-2-data"><?php echo $itemcode; ?></td>
			<td class="sticky-column sticky-column-3-data"><?php echo $INVROW['item_description']; ?></td>
			<td style="text-align:center" class="sticky-column sticky-column-4-data"><?php echo $INVROW['uom']; ?></td>
			<td style="text-align:right;" class="sticky-column sticky-column-5-data"><?php echo $beginning?></td>		
			
<?php
		$TotalGetInventoryIn=$TotalGetInventoryOut=$TotalGetInventoryReturn=$TotalGetInventoryDamage=$TotalGetInventoryComplementary=$TotalGetInventoryBadorder=$TotalGetInventoryCharges=$TotalGetInventoryRnd=0;
		for($z=1; $z<= $numDays; $z++){ 
			$datetin = date("Y-m-d", strtotime($year."-".$month."-".$z));	
			$GetInventoryIn = $inventory->GetInventoryIn($datetin,$itemcode,$db);
			$TotalGetInventoryIn +=	$GetInventoryIn;
?>
	    	<td style="text-align:right" class="psahovin" ondblclick="timeInDetails('<?php echo $itemcode?>','<?php echo $datetin?>')"><?php echo $GetInventoryIn?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryIn,2); ?></td>
<?php
		//RETURN
		for($ret=1; $ret<= $numDays; $ret++){ 
			$datereturn = date("Y-m-d", strtotime($year."-".$month."-".$ret));	
			$GetInventoryReturn = $inventory->GetInventoryOtherData('return','quantity',$datereturn, $itemcode, $db);
			$TotalGetInventoryReturn +=	$GetInventoryReturn;
?>
	    	<td style="text-align:right" class="psahovreturn" ondblclick="otherDetails('return','INVENTORY RETURN','<?php echo $itemcode?>','<?php echo $datereturn?>')"><?php echo $GetInventoryReturn?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryReturn,2); ?></td>
<?php
		//DAMAGE
		for($dam=1; $dam<= $numDays; $dam++){ 
			$datedamage = date("Y-m-d", strtotime($year."-".$month."-".$dam));	
			$GetInventoryDamage = $inventory->GetInventoryOtherData('damage','quantity',$datedamage, $itemcode, $db);
			$TotalGetInventoryDamage +=	$GetInventoryDamage;
?>
	    	<td style="text-align:right" class="psahovdamage" ondblclick="otherDetails('damage','INVENTORY DAMAGE','<?php echo $itemcode?>','<?php echo $datedamage?>')"><?php echo $GetInventoryDamage?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryDamage,2); ?></td>
<?php
		//COMPLEMENTARY
		for($com=1; $com<= $numDays; $com++){ 
			$datecomplementary = date("Y-m-d", strtotime($year."-".$month."-".$com));	
			$GetInventoryComplementary = $inventory->GetInventoryOtherData('complementary','quantity',$datecomplementary, $itemcode, $db);
			$TotalGetInventoryComplementary +=	$GetInventoryComplementary;
?>
	    	<td style="text-align:right" class="psahovcomplementary" ondblclick="otherDetails('complementary','INVENTORY COMPLEMENTARY','<?php echo $itemcode?>','<?php echo $datecomplementary?>')"><?php echo $GetInventoryComplementary?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryComplementary,2); ?></td>
<?php
		//BADORDER
		for($bad=1; $bad<= $numDays; $bad++){ 
			$datebadorder = date("Y-m-d", strtotime($year."-".$month."-".$bad));	
			$GetInventoryBadorder = $inventory->GetInventoryOtherData('badorder','quantity',$datebadorder, $itemcode, $db);
			$TotalGetInventoryBadorder +=	$GetInventoryBadorder;
?>
	    	<td style="text-align:right" class="psahovbadorder" ondblclick="otherDetails('badorder','INVENTORY BAD ORDER','<?php echo $itemcode?>','<?php echo $datebadorder?>')"><?php echo $GetInventoryBadorder?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryBadorder,2); ?></td>

<?php
		//CHARGES
		for($cha=1; $cha<= $numDays; $cha++){ 
			$datecharges = date("Y-m-d", strtotime($year."-".$month."-".$cha));	
			$GetInventoryCharges = $inventory->GetInventoryOtherData('charges','quantity',$datecharges, $itemcode, $db);
			$TotalGetInventoryCharges +=	$GetInventoryCharges;
?>
	    	<td style="text-align:right" class="psahovcharges" ondblclick="otherDetails('charges','INVENTORY CHARGES','<?php echo $itemcode?>','<?php echo $datecharges?>')"><?php echo $GetInventoryCharges?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryCharges,2); ?></td>
<?php
		//RND
		for($rn =1; $rn <= $numDays; $rn++){ 
			$daternd = date("Y-m-d", strtotime($year."-".$month."-".$rn));	
			$GetInventoryRnd = $inventory->GetInventoryOtherData('rnd','quantity',$daternd, $itemcode, $db);
			$TotalGetInventoryRnd +=	$GetInventoryRnd;
?>
	    	<td style="text-align:right" class="psahovrnd" ondblclick="otherDetails('rnd','INVENTORY R&amp;D','<?php echo $itemcode?>','<?php echo $daternd?>')"><?php echo $GetInventoryRnd?></td>
<?php	} ?>
		<td style="text-align:right"><?php echo number_format($TotalGetInventoryRnd,2); ?></td>	

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
	$.post("./Modules/DBC_Management/apps/inventory_others_form.php", { itemcode: itemcode, transdate: transdate, table: table },
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
	$.post("./Modules/DBC_Management/apps/inventory_out_form.php", { itemcode: itemcode, transdate: transdate },
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
	$.post("./Modules/DBC_Management/apps/inventory_in_form.php", { itemcode: itemcode, transdate: transdate },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});

}

</script>
